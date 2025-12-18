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
     * Override redirect to use admin route
     * Always redirect to admin.profile route regardless of the provided route parameter
     */
    protected function redirectWithSuccess(string $route, string $message)
    {
        return redirect()->route('admin.profile')->with('success', $message);
    }
}
