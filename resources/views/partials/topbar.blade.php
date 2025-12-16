<header class="ls-topbar">
    <div class="ls-brand">
        <div class="logo">LS</div>
        @auth
        <h2>{{ $t('messages.dashboard') }}</h2>
        @else
        <h2>{{ $settings['site_name'] ?? config('app.name', 'sawa') }}</h2>
        @endauth
    </div>
    <div class="ls-topbar-actions">
        @include('partials.language-switcher')

        @auth
        <a href="{{ (method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin()) ? route('admin.dashboard') : route('user.dashboard') }}" class="lang-switch">{{ $t('messages.dashboard') }}</a>
        <form method="POST" action="{{ route('logout') }}">@csrf
            <button type="submit" class="lang-switch">{{ $t('messages.logout') }}</button>
        </form>
        @else
        <a href="{{ route('login') }}" class="lang-switch">{{ $t('messages.login') }}</a>
        <a href="{{ route('register.step1') }}" class="lang-switch">{{ $t('messages.register') }}</a>
        @endauth
    </div>
</header>