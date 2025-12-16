<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ValidatesRecaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
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

        $status = Password::sendResetLink($data);

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('success', TranslationHelper::get('messages.reset_link_sent'));
        }

        return back()->with('error', TranslationHelper::get('messages.reset_link_failed'))->withInput();
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
