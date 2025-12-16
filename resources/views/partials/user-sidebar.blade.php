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
        <!-- Control Panel & Statistics -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.sidebar_control_panel') }}</div>
            <a href="{{ route('user.dashboard') }}" class="menu-item {{ $currentRoute === 'user.dashboard' ? 'active' : '' }}">
                <i class="fa-solid fa-home icon"></i>
                <span class="label">{{ $t('messages.dashboard') }}</span>
            </a>
            <a href="{{ route('user.orders.statistics') }}" class="menu-item {{ $currentRoute === 'user.orders.statistics' || str_contains($currentRoute, 'orders.statistics') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line icon"></i>
                <span class="label">{{ $t('messages.sidebar_order_statistics') }}</span>
            </a>
            <a href="{{ route('user.ai-analytics.index') }}" class="menu-item {{ $currentRoute === 'user.ai-analytics.index' || str_contains($currentRoute, 'ai-analytics') ? 'active' : '' }}">
                <i class="fa-solid fa-bolt icon"></i>
                <span class="label">{{ $t('messages.sidebar_ai_analytics') }}</span>
            </a>
        </div>

        <!-- Products & Pages -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.sidebar_products_pages') }}</div>
            <a href="{{ route('user.products.index') }}" class="menu-item {{ str_contains($currentRoute, 'products') ? 'active' : '' }}">
                <i class="fa-solid fa-shopping-cart icon"></i>
                <span class="label">{{ $t('messages.products') }}</span>
                @if($productsCount > 0)
                <span class="badge">{{ $productsCount }}</span>
                @endif
            </a>
            <a href="{{ route('user.pages.index') }}" class="menu-item {{ str_contains($currentRoute, 'pages') ? 'active' : '' }}">
                <i class="fa-solid fa-link icon"></i>
                <span class="label">{{ $t('messages.pages') }}</span>
                @if($pagesCount > 0)
                <span class="badge">{{ $pagesCount }}</span>
                @endif
            </a>
        </div>

        <!-- Order Management -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.sidebar_order_management') }}</div>
            <a href="{{ route('user.orders.index') }}" class="menu-item {{ $currentRoute === 'user.orders.index' ? 'active' : '' }}">
                <i class="fa-solid fa-shopping-cart icon"></i>
                <span class="label">{{ $t('messages.orders') }}</span>
                @if(($ordersCount ?? 0) > 0)
                <span class="badge">{{ $ordersCount }}</span>
                @endif
            </a>
            <a href="{{ route('user.orders.abandoned') }}" class="menu-item {{ str_contains($currentRoute, 'orders.abandoned') ? 'active' : '' }}">
                <i class="fa-solid fa-shopping-cart icon"></i>
                <span class="label">{{ $t('messages.sidebar_abandoned_orders') }}</span>
                @if(($abandonedOrdersCount ?? 0) > 0)
                <span class="badge">{{ $abandonedOrdersCount }}</span>
                @endif
            </a>
        </div>

        <!-- Integrations -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.sidebar_integrations') }}</div>
            <a href="#" class="menu-item">
                <i class="fa-solid fa-link icon"></i>
                <span class="label">{{ $t('messages.sidebar_connect_taager') }}</span>
            </a>
            <a href="{{ route('user.facebook-conversion-api.index') }}" class="menu-item {{ str_contains($currentRoute, 'facebook-conversion-api') ? 'active' : '' }}">
                <i class="fa-brands fa-facebook icon"></i>
                <span class="label">{{ $t('messages.sidebar_facebook_conversion') ?? 'Facebook Conversion API' }}</span>
            </a>
            <a href="{{ route('user.webhooks.index') }}" class="menu-item {{ str_contains($currentRoute, 'webhooks') ? 'active' : '' }}">
                <i class="fa-solid fa-bolt icon"></i>
                <span class="label">{{ $t('messages.sidebar_webhook_notifications') ?? 'Webhooks' }}</span>
            </a>
        </div>

        <!-- Contact & Support -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.sidebar_contact_support') }}</div>
            <a href="#" class="menu-item">
                <i class="fa-solid fa-envelope icon"></i>
                <span class="label">{{ $t('messages.sidebar_messages') }}</span>
            </a>
            <a href="#" class="menu-item">
                <i class="fa-solid fa-info-circle icon"></i>
                <span class="label">{{ $t('messages.sidebar_support') }}</span>
            </a>
        </div>

        <!-- Account & Billing -->
        <div class="menu-section">
            <div class="section-title">{{ $t('messages.sidebar_account_billing') }}</div>
            <a href="{{ route('user.profile') }}" class="menu-item {{ str_contains($currentRoute, 'profile') ? 'active' : '' }}">
                <i class="fa-solid fa-cog icon"></i>
                <span class="label">{{ $t('messages.sidebar_personal_settings') }}</span>
            </a>
            <a href="{{ route('user.packages.index') }}" class="menu-item {{ str_contains($currentRoute, 'packages') ? 'active' : '' }}">
                <i class="fa-solid fa-gift icon"></i>
                <span class="label">{{ $t('messages.plans') }}</span>
                <span class="plan-badge">{{ strtoupper($userPackageName) }}</span>
            </a>
            <a href="{{ route('user.wallet.topup') }}" class="menu-item {{ str_contains($currentRoute ?? '', 'wallet') || str_contains($currentRoute ?? '', 'topup') ? 'active' : '' }}">
                <i class="fa-solid fa-dollar-sign icon"></i>
                <span class="label">{{ $t('messages.sidebar_add_balance') }}</span>
            </a>
            <a href="{{ route('user.transactions') }}" class="menu-item {{ str_contains($currentRoute, 'transactions') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar icon"></i>
                <span class="label">{{ $t('messages.sidebar_invoices_operations') }}</span>
            </a>
        </div>
    </nav>

    <!-- User Profile Footer -->
    <div class="sidebar-footer">
        <div class="user-profile-info">
            <div class="user-avatar">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div class="user-details">
                <div class="user-name">{{ $user->name }}</div>
                <div class="user-plan">{{ strtoupper($userPackageName) }}</div>
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