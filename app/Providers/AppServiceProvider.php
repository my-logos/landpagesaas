<?php

namespace App\Providers;

use App\Models\SubscriptionPackage;
use App\Services\SubscriptionService;
use App\Services\SettingsService;
use App\View\Composers\AppLayoutComposer;
use App\View\Composers\GuestLayoutComposer;
use App\View\Composers\LandingPageComposer;
use App\View\Composers\UserSidebarComposer;
use App\View\Composers\AdminSidebarComposer;
use App\View\Composers\TopbarComposer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind SettingsService as singleton
        $this->app->singleton(SettingsService::class, function ($app) {
            return new SettingsService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register view composers
        View::composer('layouts.app', AppLayoutComposer::class);
        View::composer('layouts.guest', GuestLayoutComposer::class);
        View::composer('auth.*', GuestLayoutComposer::class);
        View::composer('landing', LandingPageComposer::class);
        View::composer('partials.user-sidebar', UserSidebarComposer::class);
        View::composer('partials.sidebar', AdminSidebarComposer::class);
        View::composer('partials.topbar', TopbarComposer::class);

        // Share settings and translation function with all views
        View::composer('*', function ($view) {
            $settingsService = app(SettingsService::class);
            $view->with('settings', $settingsService->all());

            // Make translation function available globally - always set it to ensure it's available
            $locale = app()->getLocale();
            if (!in_array($locale, ['en', 'ar'])) {
                $locale = 'en';
                app()->setLocale('en');
                session(['locale' => 'en']);
            }

            $t = function ($key) {
                return \App\Helpers\TranslationHelper::get($key);
            };

            // Always set $t to ensure it's available in all views
            $view->with('t', $t);
        });

        // When a user registers: assign default role and auto-subscribe to free package if available.
        Event::listen(Registered::class, function (Registered $event): void {
            $user = $event->user;

            // assign 'user' role if available
            if (method_exists($user, 'assignRole')) {
                try {
                    $user->assignRole('user');
                } catch (\Throwable $e) {
                    // ignore if roles table not set up yet
                }
            } else {
                $user->role = 'user';
                $user->save();
            }

            // Auto-subscribe to free package if exists
            $free = SubscriptionPackage::where('is_free', true)->first();

            $service = app(SubscriptionService::class);

            if ($free) {
                // ensure user active
                $user->is_active = true;
                $user->save();

                try {
                    $service->subscribeUserToPackage($user, $free->id);
                } catch (\Throwable $e) {
                    // swallow subscription errors; admin can fix
                }
            } else {
                // no free package: mark user inactive until paid
                $user->is_active = false;
                $user->save();
            }
        });
    }
}
