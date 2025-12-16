@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.settings') }}</h1>
    </div>

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" id="settingsForm">
            @csrf
            @method('PUT')

            <!-- Logo Upload (Shared) -->
            <div class="form-section">
                <h3>{{ $t('messages.site_logo') ?? 'Site Logo' }}</h3>
                <div class="form-group">
                    <input type="file" id="site_logo" name="site_logo" class="form-control" accept="image/*">
                    @if($firstLogo)
                    <div class="logo-preview">
                        <img src="{{ asset($firstLogo) }}" alt="Logo" class="logo-preview-image">
                        <input type="hidden" name="site_logo_url" value="{{ $firstLogo }}">
                    </div>
                    @endif
                </div>
            </div>

            <!-- Language Tabs -->
            <div class="profile-tabs">
                @foreach($languages as $lang)
                <a href="#" class="tab-item {{ $loop->first ? 'active' : '' }}" data-tab="{{ $lang->code }}">
                    <i class="fa-solid fa-globe"></i>
                    <span>{{ $lang->native_name ?? $lang->name }}</span>
                </a>
                @endforeach
            </div>

            <!-- Settings Content for each language (Tabs) -->
            @foreach($languages as $lang)
            <div class="settings-tab-content {{ $loop->first ? 'active' : '' }}" data-tab-content="{{ $lang->code }}">
                <input type="hidden" name="settings[{{ $lang->code }}][locale]" value="{{ $lang->code }}">

                <!-- Site Information -->
                <div class="form-section">
                    <h3>{{ $t('messages.site_information') ?? 'Site Information' }}</h3>

                    <div class="form-group">
                        <label for="site_name_{{ $lang->code }}">{{ $t('messages.site_name') ?? 'Site Name' }} *</label>
                        <input type="text" id="site_name_{{ $lang->code }}" name="settings[{{ $lang->code }}][site_name]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['site_name'] ?? '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="site_email_{{ $lang->code }}">{{ $t('messages.site_email') ?? 'Site Email' }} *</label>
                        <input type="email" id="site_email_{{ $lang->code }}" name="settings[{{ $lang->code }}][site_email]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['site_email'] ?? '' }}" required>
                    </div>

                    <div class="form-group">
                        <label for="site_phone_{{ $lang->code }}">{{ $t('messages.site_phone') ?? 'Site Phone' }}</label>
                        <input type="text" id="site_phone_{{ $lang->code }}" name="settings[{{ $lang->code }}][site_phone]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['site_phone'] ?? '' }}">
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="form-section">
                    <h3>{{ $t('messages.seo_settings') ?? 'SEO Settings' }}</h3>

                    <div class="form-group">
                        <label for="seo_title_{{ $lang->code }}">{{ $t('messages.seo_title') ?? 'SEO Title' }}</label>
                        <input type="text" id="seo_title_{{ $lang->code }}" name="settings[{{ $lang->code }}][seo_title]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['seo_title'] ?? '' }}">
                    </div>

                    <div class="form-group">
                        <label for="seo_description_{{ $lang->code }}">{{ $t('messages.seo_description') ?? 'SEO Description' }}</label>
                        <textarea id="seo_description_{{ $lang->code }}" name="settings[{{ $lang->code }}][seo_description]" class="form-control" rows="3">{{ $settingsByLocale[$lang->code]['seo_description'] ?? '' }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="seo_keywords_{{ $lang->code }}">{{ $t('messages.seo_keywords') ?? 'SEO Keywords' }}</label>
                        <input type="text" id="seo_keywords_{{ $lang->code }}" name="settings[{{ $lang->code }}][seo_keywords]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['seo_keywords'] ?? '' }}"
                            placeholder="landing pages, e-commerce, saas">
                    </div>
                </div>

                <!-- General Settings -->
                <div class="form-section">
                    <h3>{{ $t('messages.general_settings') ?? 'General Settings' }}</h3>

                    <div class="form-group">
                        <label for="currency_code_{{ $lang->code }}">{{ $t('messages.currency_code') ?? 'Currency Code' }} *</label>
                        <input type="text" id="currency_code_{{ $lang->code }}" name="settings[{{ $lang->code }}][currency_code]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['currency_code'] ?? 'USD' }}" required placeholder="USD, EGP, EUR, etc.">
                    </div>

                    <div class="form-group">
                        <label for="default_language_{{ $lang->code }}">{{ $t('messages.default_language') ?? 'Default Language' }} *</label>
                        <select id="default_language_{{ $lang->code }}" name="settings[{{ $lang->code }}][default_language]" class="form-control" required>
                            @foreach($allLanguages as $optLang)
                            <option value="{{ $optLang->code }}" {{ ($settingsByLocale[$lang->code]['default_language'] ?? 'en') === $optLang->code ? 'selected' : '' }}>
                                {{ $optLang->native_name ?? $optLang->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="form-actions visible">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-settings.js') }}"></script>
@endpush
@endsection