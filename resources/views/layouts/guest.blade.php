<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings['seo_title'] ?? $settings['site_name'] ?? config('app.name', 'sawa') }}</title>
    <meta name="description" content="{{ $settings['seo_description'] ?? '' }}">
    <meta name="keywords" content="{{ $settings['seo_keywords'] ?? '' }}">
    <!-- Font Awesome 7 - Latest Version with Colors -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/landing-saas.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing-home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/loading-bar.css') }}">
    @stack('head')
</head>

<body class="ls-guest-body">
    <!-- Navigation for guest pages -->
    <header class="ls-guest-header">
        <div class="ls-guest-nav">
        </div>
    </header>

    <main class="ls-guest-main">
        @if(session('success'))
        <div data-flash-message="{{ session('success') }}" data-flash-type="success"></div>
        @endif

        @if(session('error'))
        <div data-flash-message="{{ session('error') }}" data-flash-type="error"></div>
        @endif

        @if(session('status'))
        <div data-flash-message="{{ session('status') }}" data-flash-type="success"></div>
        @endif

        @if(session('warning'))
        <div data-flash-message="{{ session('warning') }}" data-flash-type="warning"></div>
        @endif

        @if(session('info'))
        <div data-flash-message="{{ session('info') }}" data-flash-type="info"></div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="ls-guest-footer">
        <div class="footer-content">
            <p>&copy; 2025 sawa. {{ \App\Helpers\TranslationHelper::get('messages.footer_rights', 'All rights reserved') }}</p>
        </div>
    </footer>

    <div class="floating-actions">
        <div class="fab" title="help">?</div>
    </div>

    <script src="{{ asset('js/landing-saas.js') }}"></script>
    <script src="{{ asset('js/language-switcher.js') }}"></script>
    <script src="{{ asset('js/loading-bar.js') }}"></script>
    @if(request()->routeIs('register.step2'))
    <script src="{{ asset('js/auth-phone-validation.js') }}"></script>
    @endif
    @stack('scripts')
</body>

</html>