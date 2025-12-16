<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'is_active' => 'boolean',
            'email_verified_at' => 'nullable|date',
        ]);

        // Handle email verification status
        if (isset($validated['email_verified_at'])) {
            $validated['email_verified_at'] = $validated['email_verified_at'] === 'now' ? now() : null;
        }

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
}
