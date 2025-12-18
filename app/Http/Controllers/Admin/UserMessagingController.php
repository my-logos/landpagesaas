<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CustomUserMessage;

class UserMessagingController extends BaseAdminController
{
    public function index()
    {
        $packages = SubscriptionPackage::orderBy('name')->get();
        return view('admin.users.messaging', $this->getViewData(compact('packages')));
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'filter_type' => 'required|in:all,active,inactive,verified,unverified,package',
            'package_id' => 'nullable|required_if:filter_type,package|exists:subscription_packages,id',
        ]);

        // Build query based on filter
        $query = User::where('role', '!=', 'admin')->orWhereNull('role');

        switch ($validated['filter_type']) {
            case 'active':
                $query->where('is_active', true);
                break;
            case 'inactive':
                $query->where('is_active', false);
                break;
            case 'verified':
                $query->whereNotNull('email_verified_at');
                break;
            case 'unverified':
                $query->whereNull('email_verified_at');
                break;
            case 'package':
                $query->whereHas('subscriptions', function ($q) use ($validated) {
                    $q->where('package_id', $validated['package_id'])
                        ->where('status', 'active');
                });
                break;
        }

        $users = $query->whereNotNull('email')->get();

        if ($users->isEmpty()) {
            return $this->redirectBackWithError('messages.no_users_found', 'No users found matching the criteria');
        }

        $sentCount = 0;
        $failedCount = 0;
        $errors = [];

        foreach ($users as $user) {
            try {
                // Replace {name} placeholder in message and subject for each user
                $personalizedMessage = str_replace('{name}', $user->name, $validated['message']);
                $personalizedSubject = str_replace('{name}', $user->name, $validated['subject']);

                // Validate email address
                if (empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                    $failedCount++;
                    $errors[] = "Invalid email for user {$user->id}: {$user->email}";
                    Log::warning('Skipping user with invalid email', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);
                    continue;
                }

                // Send email - use queue if available, otherwise send directly
                Mail::to($user->email)->send(new CustomUserMessage(
                    $user,
                    $personalizedSubject,
                    $personalizedMessage
                ));

                $sentCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                $errorMsg = $e->getMessage();
                $errors[] = "User {$user->id} ({$user->email}): {$errorMsg}";
                Log::error('Failed to send custom message to user', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $errorMsg,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Check mail configuration
        $mailDriver = config('mail.default');
        $mailFrom = config('mail.from.address');

        $message = "Messages sent: {$sentCount}";
        if ($failedCount > 0) {
            $message .= ", Failed: {$failedCount}";
        }

        // Add warning if mail driver is 'log'
        if ($mailDriver === 'log') {
            $message .= ". ⚠️ WARNING: Mail driver is set to 'log'. Emails are being saved to log file instead of being sent. Please configure SMTP in .env file.";
        }

        $response = redirect()->route('admin.users.messaging')
            ->with('success', $message)
            ->with('sent_count', $sentCount)
            ->with('failed_count', $failedCount);

        // Store errors in session for debugging
        if (!empty($errors) && count($errors) <= 10) {
            $response->with('errors', $errors);
        } elseif (!empty($errors)) {
            $response->with('errors', array_slice($errors, 0, 10))
                ->with('errors_more', count($errors) - 10);
        }

        return $response;
    }
}
