<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\User\ProfileController as BaseProfileController;
use Illuminate\Http\Request;

/**
 * Admin uses the same logic as User with different route/view names
 * This reduces duplication and makes maintenance easier
 */
class ProfileController extends BaseProfileController
{
    /**
     * Override the route prefix
     */
    protected function getRoutePrefix(): string
    {
        return 'admin';
    }

    /**
     * Override the view name
     */
    protected function getViewName(): string
    {
        return 'admin.profile';
    }

    public function index()
    {
        $user = auth()->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        return view($this->getViewName(), compact('user', 'locale', 'dir', 't'));
    }

    /**
     * تجاوز redirect مع الـ admin route
     */
    protected function redirectWithSuccess(string $route, string $message)
    {
        return redirect()->route('admin.profile')->with('success', $message);
    }
}
