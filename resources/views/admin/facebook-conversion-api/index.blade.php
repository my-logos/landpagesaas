@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="page-header">
        <h1 class="page-title">
            <span class="pro-badge">PRO</span>
            {{ $t('messages.facebook_conversion_api') ?? 'Facebook Conversion API' }}
            <i class="fa-brands fa-facebook"></i>
        </h1>
        <p class="dashboard-subtitle">
            {{ $t('messages.conversion_api_system_settings') ?? 'Facebook Conversion API settings for system landing page - linked to main landing page' }}
        </p>
    </div>

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
                        ? ($t('messages.conversion_api_system_enabled') ?? 'Facebook Conversion API is enabled and working on system landing page')
                        : ($t('messages.setup_conversion_api_system') ?? 'Set up Facebook Conversion API to improve conversion tracking accuracy on system landing page') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <div class="settings-section">
        <div class="ls-card">
            <h2 class="section-title">{{ $t('messages.conversion_api_settings') ?? 'Facebook Conversion API Settings' }}</h2>
            <form method="POST" action="{{ route('admin.facebook-conversion-api.update') }}" id="facebookConversionAPIForm">
                @csrf
                @method('PUT')

                <!-- Pixel ID -->
                <div class="form-group">
                    <label for="pixel_id" class="form-label">
                        {{ $t('messages.pixel_id_label') ?? 'Pixel ID' }} *
                    </label>
                    <input type="text" id="pixel_id" name="pixel_id"
                        class="form-control" value="{{ $fbConversionAPISettings->pixel_id ?? '' }}"
                        placeholder="123456789012345" {{ ($fbConversionAPISettings->is_enabled ?? false) ? 'required' : '' }}>
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
                        class="form-control" value="{{ $fbConversionAPISettings->access_token ?? '' }}"
                        placeholder="EAA..." {{ ($fbConversionAPISettings->is_enabled ?? false) ? 'required' : '' }}>
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
                        class="form-control" value="{{ $fbConversionAPISettings->test_event_code ?? '' }}"
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
                        <option value="EGP" {{ ($fbConversionAPISettings->currency ?? 'EGP') === 'EGP' ? 'selected' : '' }}>{{ $t('messages.egyptian_pound') ?? 'Egyptian Pound (EGP)' }}</option>
                        <option value="USD" {{ ($fbConversionAPISettings->currency ?? '') === 'USD' ? 'selected' : '' }}>{{ $t('messages.us_dollar') ?? 'US Dollar (USD)' }}</option>
                        <option value="EUR" {{ ($fbConversionAPISettings->currency ?? '') === 'EUR' ? 'selected' : '' }}>{{ $t('messages.euro') ?? 'Euro (EUR)' }}</option>
                        <option value="SAR" {{ ($fbConversionAPISettings->currency ?? '') === 'SAR' ? 'selected' : '' }}>{{ $t('messages.saudi_riyal') ?? 'Saudi Riyal (SAR)' }}</option>
                        <option value="AED" {{ ($fbConversionAPISettings->currency ?? '') === 'AED' ? 'selected' : '' }}>{{ $t('messages.uae_dirham') ?? 'UAE Dirham (AED)' }}</option>
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
                            {{ ($fbConversionAPISettings->is_enabled ?? false) ? 'checked' : '' }}>
                        <span>{{ $t('messages.enable_conversion_api_system') ?? 'Enable Facebook Conversion API on system landing page' }}</span>
                    </label>
                    <div class="form-info">
                        <i class="fa-solid fa-info-circle"></i>
                        <span>{{ $t('messages.enable_conversion_api_system_desc') ?? 'When enabled, these settings will be automatically linked to the system landing page.' }}</span>
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
</div>
@endsection

@push('styles')
<!-- Styles included in main.css -->
@endpush