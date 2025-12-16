<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Helpers\TranslationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use HasLocaleAndTranslation;

    public function index()
    {
        $user = auth()->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        return view('user.profile', compact('user', 'locale', 'dir', 't'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $validated = $this->validateProfileData($request, $user);
        $user->update($validated);

        return $this->redirectWithSuccess(
            'user.profile',
            $this->getTranslatedMessage('messages.profile_updated', 'Profile updated successfully')
        );
    }

    /**
     * Validate profile data
     */
    protected function validateProfileData(Request $request, $user): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();
        $validated = $this->validatePasswordData($request);
        $this->updateUserPassword($user, $validated['password']);

        return $this->redirectWithSuccess(
            'user.profile',
            $this->getTranslatedMessage('messages.password_updated', 'Password updated successfully')
        );
    }

    /**
     * Validate password data
     */
    protected function validatePasswordData(Request $request): array
    {
        return $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);
    }

    /**
     * Update user password
     */
    protected function updateUserPassword($user, string $password): void
    {
        $user->update(['password' => Hash::make($password)]);
    }

    /**
     * Helper to redirect with success message
     */
    protected function redirectWithSuccess(string $route, string $message)
    {
        return redirect()->route($route)->with('success', $message);
    }

    /**
     * Get translated message with fallback
     *
     * @param string $messageKey Translation key
     * @param string $fallback Fallback message
     * @return string
     */
    protected function getTranslatedMessage(string $messageKey, string $fallback): string
    {
        return TranslationHelper::get($messageKey) ?? $fallback;
    }
}
