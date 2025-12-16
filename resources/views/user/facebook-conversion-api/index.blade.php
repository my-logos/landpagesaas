@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">
            <span class="pro-badge">PRO</span>
            {{ $t('messages.facebook_conversion_api') ?? 'Facebook Conversion API' }}
            <i class="fa-brands fa-facebook"></i>
        </h1>
    </div>

    @if(!$hasFacebookConversionAPI)
    <!-- Restricted Access Section -->
    <div class="restricted-access-box">
        <div class="restricted-content">
            <div class="warning-icon">
                <i class="fa-solid fa-exclamation-circle"></i>
            </div>
            <h2 class="restricted-title">{{ $t('messages.access_restricted') ?? 'Access Restricted' }}</h2>
            <p class="restricted-message">
                {{ $t('messages.pro_plans_only') ?? 'This feature is available for Pro plans only. Please upgrade your subscription.' }}
            </p>
            <a href="{{ route('upgrade.required', ['feature' => 'facebook_conversion_api']) }}" class="btn-upgrade">
                <i class="fa-solid fa-arrow-up"></i>
                {{ $t('messages.upgrade_subscription') ?? 'Upgrade Subscription' }}
            </a>
        </div>
    </div>
    @endif

    @if($hasFacebookConversionAPI)
    <!-- Connection Status Box -->
    <div class="connection-status-box {{ $isConnected ? 'connected' : 'not-connected' }}">
        <div class="status-content">
            <div class="status-icon">
                @if($isConnected)
                <i class="fa-solid fa-check-circle"></i>
                @else
                <i class="fa-solid fa-times-circle"></i>
                @endif
            </div>
            <div class="status-text">
                <h3 class="status-title">{{ $isConnected ? ($t('messages.connected') ?? 'Connected') : ($t('messages.not_connected') ?? 'Not Connected') }}</h3>
                <p class="status-message">
                    {{ $isConnected 
                        ? ($t('messages.conversion_api_enabled_working') ?? 'Facebook Conversion API is enabled and working automatically on all your landing pages')
                        : ($t('messages.setup_conversion_api') ?? 'Set up Facebook Conversion API to improve conversion tracking accuracy') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Information Section -->
    <div class="info-section">
        <div class="info-header collapsed" data-toggle-info="true">
            <i class="fa-solid fa-chevron-down info-toggle"></i>
            <span>{{ $t('messages.info_about_conversion_api') ?? 'Information about Facebook Conversion API' }}</span>
            <i class="fa-solid fa-circle-info"></i>
        </div>
        <div class="info-cards-container hidden">
            <div class="info-cards-grid">
                <!-- Card 1: What is Facebook Conversion API -->
                <div class="info-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-wave-square"></i>
                    </div>
                    <h4 class="card-title">{{ $t('messages.what_is_conversion_api') ?? 'What is Facebook Conversion API?' }}</h4>
                    <p class="card-content">
                        {{ $t('messages.what_is_conversion_api_desc') ?? 'Facebook Conversion API is a tool that allows you to send conversion events directly from the server to Facebook, which improves tracking accuracy and reduces the impact of ad blockers and browser restrictions.' }}
                    </p>
                </div>

                <!-- Card 2: Main Benefits -->
                <div class="info-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-gem"></i>
                    </div>
                    <h4 class="card-title">{{ $t('messages.main_benefits') ?? 'Main Benefits' }}</h4>
                    <ul class="card-list">
                        <li>{{ $t('messages.improved_tracking_accuracy') ?? 'Improved conversion tracking accuracy' }}</li>
                        <li>{{ $t('messages.reduced_data_loss') ?? 'Reduced data loss' }}</li>
                        <li>{{ $t('messages.improved_ad_performance') ?? 'Improved ad performance' }}</li>
                        <li>{{ $t('messages.ad_blocker_protection') ?? 'Protection from ad blockers' }}</li>
                        <li>{{ $t('messages.privacy_compliance') ?? 'Compliance with privacy laws' }}</li>
                    </ul>
                </div>

                <!-- Card 3: How to get Access Token -->
                <div class="info-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-gear"></i>
                    </div>
                    <h4 class="card-title">{{ $t('messages.how_to_get_access_token') ?? 'How to get Access Token' }}</h4>
                    <ol class="card-list numbered">
                        <li>{{ $t('messages.go_to_business_manager') ?? 'Go to Facebook Business Manager' }}</li>
                        <li>{{ $t('messages.select_data_settings') ?? 'Select "Data Settings" - "Conversions API"' }}</li>
                        <li>{{ $t('messages.create_access_token') ?? 'Create a new Access Token' }}</li>
                        <li>{{ $t('messages.copy_paste_token') ?? 'Copy the code and paste it here' }}</li>
                        <li>{{ $t('messages.grant_permissions') ?? 'Make sure to grant the required permissions' }}</li>
                    </ol>
                </div>

                <!-- Card 4: Automatic Integration -->
                <div class="info-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h4 class="card-title">{{ $t('messages.automatic_integration') ?? 'Automatic Integration' }}</h4>
                    <p class="card-content">
                        {{ $t('messages.automatic_integration_desc') ?? 'Once Facebook Conversion API is set up, all events will be sent automatically: page view, initiate checkout, and complete purchase, while avoiding duplicate events.' }}
                    </p>
                </div>

                <!-- Card 5: Prices and Automatic Values -->
                <div class="info-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h4 class="card-title">{{ $t('messages.prices_automatic_values') ?? 'Prices and Automatic Values' }}</h4>
                    <ul class="card-list">
                        <li>{{ $t('messages.testing_uses_settings') ?? 'For testing: uses values defined in settings' }}</li>
                        <li>{{ $t('messages.actual_use_calculated') ?? 'For actual use: values are calculated automatically from real product prices' }}</li>
                        <li>{{ $t('messages.initiate_checkout_event') ?? 'Initiate Checkout event: product price + shipping cost' }}</li>
                        <li>{{ $t('messages.purchase_event') ?? 'Purchase event: final price after discounts and coupons' }}</li>
                        <li>{{ $t('messages.currency_auto_determined') ?? 'Currency: automatically determined based on store settings' }}</li>
                    </ul>
                </div>

                <!-- Card 6: Traditional Pixel vs Conversion API -->
                <div class="info-card">
                    <div class="card-icon">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h4 class="card-title">{{ $t('messages.traditional_pixel_vs_api') ?? 'Traditional Pixel vs. Conversion API' }}</h4>
                    <div class="comparison-subsection">
                        <div class="subsection-item traditional-sub">
                            <div class="subsection-header">
                                <i class="fa-solid fa-check-circle"></i>
                                <strong>{{ $t('messages.traditional_pixel_free') ?? 'Traditional Pixel (Free):' }}</strong>
                            </div>
                            <p>{{ $t('messages.traditional_pixel_desc') ?? 'You can use the traditional pixel for free on the landing page via "Additional Settings" - "Add Pixel"' }}</p>
                        </div>
                        <div class="subsection-item conversion-sub">
                            <div class="subsection-header">
                                <i class="fa-solid fa-star"></i>
                                <strong>{{ $t('messages.conversion_api_pro') ?? 'Facebook Conversion API (PRO) ★' }}</strong>
                            </div>
                            <ul class="card-list">
                                <li>{{ $t('messages.significantly_improved_results') ?? 'Significantly improved results' }}</li>
                                <li>{{ $t('messages.significantly_reduced_cost') ?? 'Significantly reduced purchase cost' }}</li>
                                <li>{{ $t('messages.recommended_by_facebook') ?? 'Recommended by Facebook instead of the traditional pixel' }}</li>
                                <li>{{ $t('messages.ad_blocker_protection') ?? 'Protection from ad blockers' }}</li>
                                <li>{{ $t('messages.higher_tracking_accuracy') ?? 'Higher tracking accuracy' }}</li>
                                <li>{{ $t('messages.better_privacy_compliance') ?? 'Better compliance with privacy laws' }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="settings-section">
        <div class="ls-card">
            <form method="POST" action="{{ route('user.facebook-conversion-api.update') }}" id="facebookConversionAPIForm">
                @csrf

                <!-- Pixel ID -->
                <div class="form-group">
                    <label for="pixel_id" class="form-label">
                        {{ $t('messages.pixel_id_label') ?? 'Pixel ID' }} *
                    </label>
                    <input type="text" id="pixel_id" name="pixel_id"
                        class="form-control" value="{{ $settings->pixel_id ?? '' }}"
                        placeholder="123456789012345" {{ ($settings->is_enabled ?? false) ? 'required' : '' }}>
                    <div class="form-info">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>{{ $t('messages.find_pixel_id') ?? 'You can find the Pixel ID in Facebook Business Manager settings' }}</span>
                    </div>
                </div>

                <!-- Access Token -->
                <div class="form-group">
                    <label for="access_token" class="form-label">
                        {{ $t('messages.access_token_label') ?? 'Access Token' }} *
                    </label>
                    <input type="password" id="access_token" name="access_token"
                        class="form-control" value="{{ $settings->access_token ?? '' }}"
                        placeholder="EAA..." {{ ($settings->is_enabled ?? false) ? 'required' : '' }}>
                    <div class="form-info">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>{{ $t('messages.access_token_desc') ?? 'Facebook Conversion API Access Token from Facebook Business Manager' }}</span>
                    </div>
                </div>

                <!-- Test Event Code -->
                <div class="form-group">
                    <label for="test_event_code" class="form-label">
                        {{ $t('messages.test_event_code') ?? 'Test Event Code' }}
                    </label>
                    <input type="text" id="test_event_code" name="test_event_code"
                        class="form-control" value="{{ $settings->test_event_code ?? '' }}"
                        placeholder="TEST12345">
                    <div class="form-info">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>{{ $t('messages.test_event_code_desc') ?? 'Optional - used to test events in Facebook Events Manager' }}</span>
                    </div>
                </div>

                <!-- Currency -->
                <div class="form-group">
                    <label for="currency" class="form-label">
                        {{ $t('messages.currency_label') ?? 'Currency' }}
                    </label>
                    <select id="currency" name="currency" class="form-control">
                        <option value="EGP" {{ ($settings->currency ?? 'EGP') === 'EGP' ? 'selected' : '' }}>{{ $t('messages.egyptian_pound') ?? 'Egyptian Pound (EGP)' }}</option>
                        <option value="USD" {{ ($settings->currency ?? '') === 'USD' ? 'selected' : '' }}>{{ $t('messages.us_dollar') ?? 'US Dollar (USD)' }}</option>
                        <option value="EUR" {{ ($settings->currency ?? '') === 'EUR' ? 'selected' : '' }}>{{ $t('messages.euro') ?? 'Euro (EUR)' }}</option>
                        <option value="SAR" {{ ($settings->currency ?? '') === 'SAR' ? 'selected' : '' }}>{{ $t('messages.saudi_riyal') ?? 'Saudi Riyal (SAR)' }}</option>
                        <option value="AED" {{ ($settings->currency ?? '') === 'AED' ? 'selected' : '' }}>{{ $t('messages.uae_dirham') ?? 'UAE Dirham (AED)' }}</option>
                    </select>
                    <div class="form-info">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>{{ $t('messages.currency_testing_desc') ?? 'This setting is optional and used only for testing events. In actual use, real product prices are used automatically.' }}</span>
                    </div>
                </div>

                <!-- Enable Toggle -->
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_enabled" value="1"
                            {{ ($settings->is_enabled ?? false) ? 'checked' : '' }}>
                        <span>{{ $t('messages.enable_conversion_api') ?? 'Enable Facebook Conversion API automatically on all landing pages' }}</span>
                    </label>
                    <div class="form-info">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>{{ $t('messages.enable_conversion_api_desc') ?? 'When enabled, these settings will be automatically linked to all your landing pages. Conversion events will be sent automatically from all pages.' }}</span>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check"></i>
                        {{ $t('messages.save_settings') ?? 'Save Settings' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/user-facebook-conversion-api.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/user-facebook-conversion-api.js') }}"></script>
@endpush