<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Helpers\TranslationHelper;

class AppLayoutComposer
{
    public function compose(View $view): void
    {
        $locale = app()->getLocale();

        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
            app()->setLocale('en');
            session(['locale' => 'en']);
        }

        $htmlDir = $locale === 'ar' ? 'rtl' : 'ltr';
        $t = function ($key) {
            return TranslationHelper::get($key);
        };

        $isAuthPage = in_array(request()->route()?->getName(), ['login', 'register.step1', 'register.step2', 'register.step3', 'password.request']);
        $user = auth()->user();
        $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
        $mainMargin = (auth()->check() && !$isAuthPage) ? '280px' : '0';

        // Calculate main content style - CSS will handle this, so we set empty style
        $mainStyle = '';

        $view->with([
            'currentLocale' => $locale,
            'htmlDir' => $htmlDir,
            't' => $t,
            'isAuthPage' => $isAuthPage,
            'isAdmin' => $isAdmin,
            'mainMargin' => $mainMargin,
            'mainStyle' => $mainStyle,
        ]);
    }
}
