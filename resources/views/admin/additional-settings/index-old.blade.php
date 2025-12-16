@extends('layouts.app')

@section('content')
<div class="user-dashboard">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Additional Settings</h1>
    </div>

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.additional-settings.update') }}" id="additionalSettingsForm">
            @csrf
            @method('PUT')

            <!-- AI Settings Tab -->
            <div class="profile-tabs">
                <a href="#" class="tab-item active" data-tab="ai">
                    <i class="fa-solid fa-robot"></i>
                    <span>AI Settings</span>
                </a>
                <a href="#" class="tab-item" data-tab="pixels">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Pixels & Analytics</span>
                </a>
                <a href="#" class="tab-item" data-tab="security">
                    <i class="fa-solid fa-shield-alt"></i>
                    <span>Security</span>
                </a>
                <a href="#" class="tab-item" data-tab="loading">
                    <i class="fa-solid fa-spinner"></i>
                    <span>Loading Bar</span>
                </a>
            </div>

            <!-- AI Settings Content -->
            <div class="settings-tab-content active" data-tab-content="ai">
                <div class="form-section">
                    <h3>{{ $t('messages.ai_provider') ?? 'AI Provider' }}</h3>
                    <div class="form-group">
                        <label for="ai_provider">{{ $t('messages.select_ai_provider') ?? 'Select AI Provider' }} *</label>
                        <select id="ai_provider" name="ai_provider" class="form-control" required>
                            <option value="gemini" {{ ($settings->ai_provider ?? 'gemini') === 'gemini' ? 'selected' : '' }}>Google Gemini</option>
                            <option value="openai" {{ ($settings->ai_provider ?? '') === 'openai' ? 'selected' : '' }}>OpenAI ChatGPT</option>
                        </select>
                    </div>

                    <h3>{{ $t('messages.gemini_api') ?? 'Google Gemini API' }}</h3>
                    <div class="form-group">
                        <label for="ai_gemini_api_key">{{ $t('messages.api_key') ?? 'API Key' }}</label>
                        <input type="text" id="ai_gemini_api_key" name="ai_gemini_api_key" class="form-control"
                            value="{{ $settings->ai_gemini_api_key ?? '' }}" placeholder="Enter Gemini API Key">
                    </div>

                    <h3>{{ $t('messages.openai_api') ?? 'OpenAI API' }}</h3>
                    <div class="form-group">
                        <label for="ai_openai_api_key">{{ $t('messages.api_key') ?? 'API Key' }}</label>
                        <input type="text" id="ai_openai_api_key" name="ai_openai_api_key" class="form-control"
                            value="{{ $settings->ai_openai_api_key ?? '' }}" placeholder="Enter OpenAI API Key">
                    </div>
                    <div class="form-group">
                        <label for="ai_openai_model">{{ $t('messages.model') ?? 'Model' }}</label>
                        <select id="ai_openai_model" name="ai_openai_model" class="form-control">
                            <option value="gpt-3.5-turbo" {{ ($settings->ai_openai_model ?? 'gpt-3.5-turbo') === 'gpt-3.5-turbo' ? 'selected' : '' }}>GPT-3.5 Turbo</option>
                            <option value="gpt-4" {{ ($settings->ai_openai_model ?? '') === 'gpt-4' ? 'selected' : '' }}>GPT-4</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Pixels & Analytics Content -->
            <div class="settings-tab-content" data-tab-content="pixels">
                <div class="form-section">
                    <h3>{{ $t('messages.facebook_pixel') ?? 'Facebook Pixel' }}</h3>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="facebook_pixel_enabled" value="1"
                                {{ ($settings->facebook_pixel_enabled ?? false) ? 'checked' : '' }}>
                            {{ $t('messages.enable') ?? 'Enable' }}
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="facebook_pixel_id">{{ $t('messages.pixel_id') ?? 'Pixel ID' }}</label>
                        <input type="text" id="facebook_pixel_id" name="facebook_pixel_id" class="form-control"
                            value="{{ $settings->facebook_pixel_id ?? '' }}" placeholder="Enter Facebook Pixel ID">
                    </div>

                    <h3>{{ $t('messages.google_analytics') ?? 'Google Analytics' }}</h3>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="google_analytics_enabled" value="1"
                                {{ ($settings->google_analytics_enabled ?? false) ? 'checked' : '' }}>
                            {{ $t('messages.enable') ?? 'Enable' }}
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="google_analytics_id">{{ $t('messages.tracking_id') ?? 'Tracking ID' }}</label>
                        <input type="text" id="google_analytics_id" name="google_analytics_id" class="form-control"
                            value="{{ $settings->google_analytics_id ?? '' }}" placeholder="G-XXXXXXXXXX">
                    </div>

                    <h3>{{ $t('messages.tiktok_pixel') ?? 'TikTok Pixel' }}</h3>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="tiktok_pixel_enabled" value="1"
                                {{ ($settings->tiktok_pixel_enabled ?? false) ? 'checked' : '' }}>
                            {{ $t('messages.enable') ?? 'Enable' }}
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="tiktok_pixel_id">{{ $t('messages.pixel_id') ?? 'Pixel ID' }}</label>
                        <input type="text" id="tiktok_pixel_id" name="tiktok_pixel_id" class="form-control"
                            value="{{ $settings->tiktok_pixel_id ?? '' }}" placeholder="Enter TikTok Pixel ID">
                    </div>

                    <h3>{{ $t('messages.snapchat_pixel') ?? 'Snapchat Pixel' }}</h3>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="snapchat_pixel_enabled" value="1"
                                {{ ($settings->snapchat_pixel_enabled ?? false) ? 'checked' : '' }}>
                            {{ $t('messages.enable') ?? 'Enable' }}
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="snapchat_pixel_id">{{ $t('messages.pixel_id') ?? 'Pixel ID' }}</label>
                        <input type="text" id="snapchat_pixel_id" name="snapchat_pixel_id" class="form-control"
                            value="{{ $settings->snapchat_pixel_id ?? '' }}" placeholder="Enter Snapchat Pixel ID">
                    </div>

                    <h3>{{ $t('messages.google_tag') ?? 'Google Tag' }}</h3>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="google_tag_enabled" value="1"
                                {{ ($settings->google_tag_enabled ?? false) ? 'checked' : '' }}>
                            {{ $t('messages.enable') ?? 'Enable' }}
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="google_tag_id">{{ $t('messages.tag_id') ?? 'Tag ID' }}</label>
                        <input type="text" id="google_tag_id" name="google_tag_id" class="form-control"
                            value="{{ $settings->google_tag_id ?? '' }}" placeholder="GT-XXXXXXXXXX">
                    </div>
                </div>
            </div>

            <!-- Security Settings Content -->
            <div class="settings-tab-content" data-tab-content="security">
                <div class="form-section">
                    <h3>{{ $t('messages.recaptcha_v2') ?? 'Google reCAPTCHA V2' }}</h3>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="recaptcha_v2_enabled" value="1"
                                {{ ($settings->recaptcha_v2_enabled ?? false) ? 'checked' : '' }}>
                            {{ $t('messages.enable') ?? 'Enable' }}
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="recaptcha_v2_site_key">{{ $t('messages.site_key') ?? 'Site Key' }}</label>
                        <input type="text" id="recaptcha_v2_site_key" name="recaptcha_v2_site_key" class="form-control"
                            value="{{ $settings->recaptcha_v2_site_key ?? '' }}" placeholder="Enter reCAPTCHA V2 Site Key">
                    </div>
                    <div class="form-group">
                        <label for="recaptcha_v2_secret_key">{{ $t('messages.secret_key') ?? 'Secret Key' }}</label>
                        <input type="text" id="recaptcha_v2_secret_key" name="recaptcha_v2_secret_key" class="form-control"
                            value="{{ $settings->recaptcha_v2_secret_key ?? '' }}" placeholder="Enter reCAPTCHA V2 Secret Key">
                    </div>

                    <h3>{{ $t('messages.recaptcha_v3') ?? 'Google reCAPTCHA V3' }}</h3>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="recaptcha_v3_enabled" value="1"
                                {{ ($settings->recaptcha_v3_enabled ?? false) ? 'checked' : '' }}>
                            {{ $t('messages.enable') ?? 'Enable' }}
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="recaptcha_v3_site_key">{{ $t('messages.site_key') ?? 'Site Key' }}</label>
                        <input type="text" id="recaptcha_v3_site_key" name="recaptcha_v3_site_key" class="form-control"
                            value="{{ $settings->recaptcha_v3_site_key ?? '' }}" placeholder="Enter reCAPTCHA V3 Site Key">
                    </div>
                    <div class="form-group">
                        <label for="recaptcha_v3_secret_key">{{ $t('messages.secret_key') ?? 'Secret Key' }}</label>
                        <input type="text" id="recaptcha_v3_secret_key" name="recaptcha_v3_secret_key" class="form-control"
                            value="{{ $settings->recaptcha_v3_secret_key ?? '' }}" placeholder="Enter reCAPTCHA V3 Secret Key">
                    </div>
                </div>
            </div>

            <!-- Loading Bar Settings Content -->
            <div class="settings-tab-content" data-tab-content="loading">
                <div class="form-section">
                    <h3>{{ $t('messages.loading_bar') ?? 'Loading Bar' }}</h3>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="loading_bar_enabled" value="1"
                                {{ ($settings->loading_bar_enabled ?? true) ? 'checked' : '' }}>
                            {{ $t('messages.enable') ?? 'Enable' }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-actions visible">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-additional-settings.js') }}"></script>
@endpush
@endsection