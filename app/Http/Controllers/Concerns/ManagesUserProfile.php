<?php

namespace App\Http\Controllers\Concerns;

use App\Helpers\TranslationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Trait for managing profile updates (User and Admin profiles)
 * Provides common update and password change methods
 */
trait ManagesUserProfile
{
    /**
     * Get locale data from the trait
     */
    protected function getProfileLocaleData()
    {
        return $this->getLocaleData();
    }

    /**
     * Validate and update user profile information
     */
    protected function updateProfileData(Request $request, $user): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return $validated;
    }

    /**
     * Validate and update user password with security checks
     */
    protected function updateUserPassword(Request $request, $user): bool
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            throw new \Exception(TranslationHelper::get('messages.current_password_incorrect', 'Current password is incorrect'));
        }

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return true;
    }
}
