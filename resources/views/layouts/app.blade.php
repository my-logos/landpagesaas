<!doctype html>
<html lang="{{ str_replace('_', '-', $currentLocale) }}" dir="{{ $htmlDir }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $settings['seo_title'] ?? $settings['site_name'] ?? config('app.name', 'Laravel') }}</title>
    <meta name="description" content="{{ $settings['seo_description'] ?? '' }}">
    <meta name="keywords" content="{{ $settings['seo_keywords'] ?? '' }}">
    <!-- Font Awesome 7 - Latest Version with Colors -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Main CSS - Unified file containing all styles -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @stack('styles')
    @stack('head')
</head>

<body class="ls-app" data-env="{{ config('app.env', 'local') }}">
    @auth
    @if(auth()->user()->isAdmin())
    @include('partials.sidebar')
    @else
    @include('partials.user-sidebar')
    @endif
    @endauth

    <div class="ls-main" id="mainContent">
        @if (!$isAuthPage)
        @include('partials.topbar')
        @endif

        <div class="container-fluid container-fluid-no-padding">
            @if(session('success'))
            <div data-flash-message="{{ session('success') }}" data-flash-type="success"></div>
            @endif

            @if(session('error'))
            <div data-flash-message="{{ session('error') }}" data-flash-type="error"></div>
            @endif

            @if(session('warning'))
            <div data-flash-message="{{ session('warning') }}" data-flash-type="warning"></div>
            @endif

            @if(session('info'))
            <div data-flash-message="{{ session('info') }}" data-flash-type="info"></div>
            @endif

            @yield('content')
        </div>
    </div>

    @include('partials.fixed-help-icon')

    @if(config('app.env') === 'production')
    <script src="{{ asset('js/console-disable.js') }}"></script>
    @endif
    <script src="{{ asset('js/landing-saas.js') }}"></script>
    <script src="{{ asset('js/language-switcher.js') }}"></script>
    <script src="{{ asset('js/loading-bar.js') }}"></script>
    @if(auth()->check() && auth()->user()->isAdmin())
    <script src="{{ asset('js/admin-common.js') }}"></script>
    @endif
    @stack('scripts')
</body>

</html>