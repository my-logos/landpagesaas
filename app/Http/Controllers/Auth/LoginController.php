<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ValidatesRecaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Mail\PasswordReset;
use App\Helpers\TranslationHelper;

class LoginController extends Controller
{
    use ValidatesRecaptcha;
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate reCAPTCHA if enabled
        $this->validateRecaptcha($request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = (bool) $request->input('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                return back()->with('error', TranslationHelper::get('messages.account_inactive'))->withInput();
            }

            return $this->redirectAfterLogin($user);
        }

        return back()->with('error', TranslationHelper::get('messages.invalid_credentials'))->withInput();
    }

    public function logout(Request $request)
    {
        // Clear all session data
        $request->session()->flush();

        // Logout user
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        return redirect('/')->with('success', TranslationHelper::get('messages.logout_success', 'Logged out successfully'));
    }

    public function showForgotForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLink(Request $request)
    {
        // Validate reCAPTCHA if enabled
        $this->validateRecaptcha($request);

        $data = $request->validate(['email' => ['required', 'email']]);

        // Find user by email
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            // Don't reveal if email exists or not for security
            return back()->with('success', TranslationHelper::get('messages.reset_link_sent', 'If that email address exists in our system, we have sent a password reset link.'));
        }

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
        try {
            Mail::to($user->email)->send(new PasswordReset($user, $token));

            return back()->with('success', TranslationHelper::get('messages.reset_link_sent', 'Password reset link has been sent to your email address.'));
        } catch (\Throwable $e) {
            Log::error('Failed to send password reset email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', TranslationHelper::get('messages.reset_link_failed', 'Failed to send password reset link. Please try again.'))->withInput();
        }
    }

    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');
        return view('auth.passwords.reset', compact('token', 'email'));
    }

    public function reset(Request $request)
    {
        // Validate reCAPTCHA if enabled
        $this->validateRecaptcha($request);

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Check if token is valid
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->with('error', TranslationHelper::get('messages.invalid_reset_token', 'Invalid or expired password reset token.'))->withInput();
        }

        // Check if token matches
        if (!hash_equals($resetRecord->token, hash('sha256', $request->token))) {
            return back()->with('error', TranslationHelper::get('messages.invalid_reset_token', 'Invalid or expired password reset token.'))->withInput();
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->with('error', TranslationHelper::get('messages.expired_reset_token', 'Password reset token has expired. Please request a new one.'))->withInput();
        }

        // Find user
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', TranslationHelper::get('messages.user_not_found', 'User not found.'))->withInput();
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete reset token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', TranslationHelper::get('messages.password_reset_success', 'Your password has been reset successfully. You can now login with your new password.'));
    }

    /**
     * Redirect user after successful login based on role
     */
    protected function redirectAfterLogin($user)
    {
        $userRole = $user->role ?? 'user';
        $successMessage = TranslationHelper::get('messages.login_success', 'Welcome back!');

        if ($userRole === 'admin' || (method_exists($user, 'isAdmin') && $user->isAdmin())) {
            return redirect()->route('admin.dashboard')->with('success', $successMessage);
        }

        return redirect()->route('user.dashboard')->with('success', $successMessage);
    }
}
