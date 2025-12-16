<aside class="ls-sidebar user-sidebar" dir="{{ $dir }}">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="logo">LS</div>
            <div class="brand-name">{{ $settings['site_name'] ?? config('app.name','sawa') }}</div>
        </div>
        <button class="ls-toggle-sidebar" type="button">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <nav class="ls-menu">
        <!-- Control Panel -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.sidebar_control_panel') }}</div>
            <a href="{{ route('admin.dashboard') }}" class="menu-item {{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
                <i class="fa-solid fa-home icon"></i>
                <span class="label">{{ $t('messages.dashboard') }}</span>
            </a>
            <a href="#" class="menu-item">
                <i class="fa-solid fa-chart-line icon"></i>
                <span class="label">{{ $t('messages.sidebar_order_statistics') }}</span>
            </a>
        </div>

        <!-- Packages Management -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.sidebar_products_pages') }}</div>
            <a href="{{ route('admin.packages.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.packages') ? 'active' : '' }}">
                <i class="fa-solid fa-gift icon"></i>
                <span class="label">{{ $t('messages.plans') }}</span>
            </a>
            <a href="{{ route('admin.products.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.products') ? 'active' : '' }}">
                <i class="fa-solid fa-box icon"></i>
                <span class="label">{{ $t('messages.products') ?? 'Products' }}</span>
            </a>
            <a href="{{ route('admin.pages.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.pages') ? 'active' : '' }}">
                <i class="fa-solid fa-file-alt icon"></i>
                <span class="label">{{ $t('messages.pages') ?? 'Pages' }}</span>
            </a>
        </div>

        <!-- Users Management -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.users_management') ?? 'Users Management' }}</div>
            <a href="{{ route('admin.users.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.users') ? 'active' : '' }}">
                <i class="fa-solid fa-users icon"></i>
                <span class="label">{{ $t('messages.users') ?? 'Users' }}</span>
            </a>
            <a href="{{ route('admin.subscriptions.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.subscriptions') ? 'active' : '' }}">
                <i class="fa-solid fa-gift icon"></i>
                <span class="label">{{ $t('messages.subscriptions') ?? 'Subscriptions' }}</span>
                @if(($pendingSubscriptionsCount ?? 0) > 0)
                <span class="badge badge-warning">{{ $pendingSubscriptionsCount }}</span>
                @endif
            </a>
        </div>

        <!-- Settings -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.sidebar_account_billing') }}</div>
            <a href="{{ route('admin.profile') }}" class="menu-item {{ str_contains($currentRoute, 'admin.profile') ? 'active' : '' }}">
                <i class="fa-solid fa-user icon"></i>
                <span class="label">{{ $t('messages.profile') }}</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.settings') && !str_contains($currentRoute, 'additional') && !str_contains($currentRoute, 'payment-gateways') ? 'active' : '' }}">
                <i class="fa-solid fa-cog icon"></i>
                <span class="label">{{ $t('messages.settings') }}</span>
            </a>
            <a href="{{ route('admin.additional-settings.index') }}" class="menu-item {{ str_contains($currentRoute, 'additional-settings') ? 'active' : '' }}">
                <i class="fa-solid fa-sliders-h icon"></i>
                <span class="label">{{ $t('messages.additional_settings') ?? 'Additional Settings' }}</span>
            </a>
            <a href="{{ route('admin.payment-gateways.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.payment-gateways') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card icon"></i>
                <span class="label">{{ $t('messages.payment_gateways') ?? 'Payment Gateways' }}</span>
            </a>
            <a href="{{ route('admin.landing-page-templates.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.landing-page-templates') ? 'active' : '' }}">
                <i class="fa-solid fa-palette icon"></i>
                <span class="label">{{ $t('messages.landing_page_templates') ?? 'Landing Page Templates' }}</span>
            </a>
            <a href="{{ route('admin.landing-page-settings.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.landing-page-settings') ? 'active' : '' }}">
                <i class="fa-solid fa-home icon"></i>
                <span class="label">{{ $t('messages.landing_page_settings') ?? 'Landing Page Settings' }}</span>
            </a>
            <a href="{{ route('admin.facebook-conversion-api.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.facebook-conversion-api') ? 'active' : '' }}">
                <i class="fa-brands fa-facebook icon"></i>
                <span class="label">{{ $t('messages.facebook_conversion_api') ?? 'Facebook Conversion API' }}</span>
            </a>
        </div>

        <!-- Languages & Translations Management -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.languages_management') ?? 'Languages Management' }}</div>
            <a href="{{ route('admin.languages.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.languages') ? 'active' : '' }}">
                <i class="fa-solid fa-globe icon"></i>
                <span class="label">{{ $t('messages.manage_languages') ?? 'Manage Languages' }}</span>
            </a>
            <a href="{{ route('admin.translations.index') }}" class="menu-item {{ str_contains($currentRoute, 'admin.translations') ? 'active' : '' }}">
                <i class="fa-solid fa-language icon"></i>
                <span class="label">{{ $t('messages.manage_translations') ?? 'Manage Translations' }}</span>
            </a>
        </div>

    </nav>

    <!-- Admin Profile Footer -->
    <div class="sidebar-footer">
        <div class="user-profile-info">
            <div class="user-avatar">
                {{ $user ? strtoupper(substr($user->name, 0, 2)) : 'AD' }}
            </div>
            <div class="user-details">
                <div class="user-name">{{ $user ? $user->name : 'Admin' }}</div>
                <div class="user-plan">{{ $t('messages.admin') ?? 'ADMIN' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-times"></i>
                <span>{{ $t('messages.logout') }}</span>
            </button>
        </form>
    </div>
</aside>