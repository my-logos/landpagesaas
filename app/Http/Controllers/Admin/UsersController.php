<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\EmailVerification;
use App\Mail\PasswordReset;
use Illuminate\Support\Facades\Response;

class UsersController extends BaseAdminController
{
    public function index(Request $request)
    {
        $users = User::where('role', '!=', 'admin')->orWhereNull('role')->paginate(15);
        return view('admin.users.index', $this->getViewData(compact('users')));
    }

    public function create()
    {
        return view('admin.users.create', $this->getViewData());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'role' => 'user',
        ]);

        return $this->redirectWithSuccess('admin.users.index', 'messages.user_created');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', $this->getViewData(compact('user')));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'nullable|string|in:user,admin',
            'is_active' => 'boolean',
            'email_verified_at' => 'nullable',
            'additional_sales_enabled' => 'boolean',
            'performance_mode' => 'nullable|string|in:fast,selective,full',
            'include_session_data' => 'boolean',
            'include_location_data' => 'boolean',
            'include_device_type' => 'boolean',
            'tips_disabled' => 'boolean',
        ]);

        // Handle email verification status
        if (isset($validated['email_verified_at'])) {
            $validated['email_verified_at'] = $validated['email_verified_at'] === 'now' || $validated['email_verified_at'] === '1' ? now() : null;
        } else {
            $validated['email_verified_at'] = $user->email_verified_at;
        }

        // Don't update wallet_balance through this form
        unset($validated['wallet_balance']);

        $user->update($validated);

        return $this->redirectWithSuccess('admin.users.index', 'messages.user_updated');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return $this->redirectWithError('admin.users.index', 'messages.cannot_delete_admin');
        }

        $user->delete();

        return $this->redirectWithSuccess('admin.users.index', 'messages.user_deleted');
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        return $this->redirectWithSuccess('admin.users.index', 'messages.user_status_updated');
    }

    public function toggleEmailVerified(User $user)
    {
        $user->update([
            'email_verified_at' => $user->email_verified_at ? null : now()
        ]);

        return $this->redirectWithSuccess('admin.users.index', 'messages.email_status_updated');
    }

    /**
     * Resend email verification link
     */
    public function resendVerificationEmail(User $user)
    {
        try {
            Mail::to($user->email)->send(new EmailVerification($user));
            return $this->redirectWithSuccess('admin.users.index', 'messages.verification_email_sent');
        } catch (\Throwable $e) {
            Log::error('Failed to send verification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            return $this->redirectWithError('admin.users.index', 'messages.verification_email_failed');
        }
    }

    /**
     * Send password reset link to user
     */
    public function sendPasswordResetLink(User $user)
    {
        try {
            // Generate password reset token
            $token = Str::random(64);

            // Store token in password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => hash('sha256', $token),
                    'created_at' => now()
                ]
            );

            // Send password reset email
            Mail::to($user->email)->send(new PasswordReset($user, $token));

            return $this->redirectWithSuccess('admin.users.index', 'messages.password_reset_link_sent');
        } catch (\Throwable $e) {
            Log::error('Failed to send password reset link', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            return $this->redirectWithError('admin.users.index', 'messages.password_reset_link_failed');
        }
    }

    /**
     * Export all user emails to CSV
     */
    public function exportEmails()
    {
        $users = User::where('role', '!=', 'admin')
            ->orWhereNull('role')
            ->whereNotNull('email')
            ->orderBy('email')
            ->get(['id', 'name', 'email', 'is_active', 'email_verified_at']);

        $filename = 'users_emails_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Add BOM for UTF-8 to ensure Excel displays Arabic correctly
        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Add headers
            fputcsv($file, ['ID', 'Name', 'Email', 'Active', 'Email Verified'], ',');

            // Add data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->is_active ? 'Yes' : 'No',
                    $user->email_verified_at ? 'Yes' : 'No'
                ], ',');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export all user phone numbers to CSV
     */
    public function exportPhones()
    {
        $users = User::where('role', '!=', 'admin')
            ->orWhereNull('role')
            ->whereNotNull('phone')
            ->orderBy('phone')
            ->get(['id', 'name', 'phone', 'is_active']);

        $filename = 'users_phones_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Add BOM for UTF-8 to ensure Excel displays Arabic correctly
        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Add headers
            fputcsv($file, ['ID', 'Name', 'Phone', 'Active'], ',');

            // Add data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->phone,
                    $user->is_active ? 'Yes' : 'No'
                ], ',');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
