@extends('layouts.app')

@section('content')
<div class="user-dashboard" dir="{{ $dir }}">
    <div class="dashboard-header">
        <h1 class="dashboard-title">{{ $t('messages.landing_page_settings') ?? 'Landing Page Settings' }}</h1>
    </div>

    <div class="ls-card">
        <form method="POST" action="{{ route('admin.landing-page-settings.update') }}" enctype="multipart/form-data" id="landingPageSettingsForm">
            @csrf
            @method('PUT')

            <!-- Hero Image Upload (Shared) -->
            <div class="form-section">
                <h3>{{ $t('messages.hero_image') ?? 'Hero Image' }}</h3>
                <div class="form-group">
                    <input type="file" id="hero_image" name="hero_image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp,image/svg+xml">
                    @if($firstHeroImage)
                    <div class="logo-preview" style="margin-top: 15px;">
                        <img src="{{ asset($firstHeroImage) }}" alt="Hero Image" class="logo-preview-image" style="max-width: 300px; border-radius: 8px; border: 2px solid #e5e7eb;">
                        <input type="hidden" name="hero_image_url" value="{{ $firstHeroImage }}">
                    </div>
                    @endif
                    <small class="form-text text-muted">
                        {{ $t('messages.hero_image_hint') ?? 'Upload an image to replace the default hero illustration' }}
                        <br>
                        <strong style="color: #dc2626;">{{ $t('messages.hero_image_required') ?? 'Note: Hero image is required if "Enable Hero Image" is checked in any language tab.' }}</strong>
                    </small>
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

                <!-- Hero Section Settings -->
                <div class="form-section">
                    <h3>{{ $t('messages.hero_section') ?? 'Hero Section' }}</h3>

                    <div class="form-group">
                        <label for="hero_title_prefix_{{ $lang->code }}">{{ $t('messages.hero_title_prefix') ?? 'Hero Title Prefix' }}</label>
                        <input type="text" id="hero_title_prefix_{{ $lang->code }}" name="settings[{{ $lang->code }}][hero_title_prefix]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['hero_title_prefix'] ?? '' }}"
                            placeholder="{{ $lang->code === 'ar' ? 'أنشئ' : 'Create' }}">
                        <small class="form-text text-muted">{{ $t('messages.hero_title_prefix_hint') ?? 'Text before animated words (e.g., "Create")' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="hero_animated_words_{{ $lang->code }}">{{ $t('messages.hero_animated_words') ?? 'Animated Words (Title)' }}</label>
                        <input type="text" id="hero_animated_words_{{ $lang->code }}" name="settings[{{ $lang->code }}][hero_animated_words]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['hero_animated_words'] ?? '' }}"
                            placeholder="{{ $lang->code === 'ar' ? 'ساعة ذكية,منتج رقمي,لعبة' : 'smart watch,digital product,game' }}">
                        <small class="form-text text-muted">{{ $t('messages.hero_animated_words_hint') ?? 'Comma-separated words that will animate in the hero title' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="hero_search_animated_words_{{ $lang->code }}">{{ $t('messages.hero_search_animated_words') ?? 'Animated Words (Search Box)' }}</label>
                        <input type="text" id="hero_search_animated_words_{{ $lang->code }}" name="settings[{{ $lang->code }}][hero_search_animated_words]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['hero_search_animated_words'] ?? '' }}"
                            placeholder="{{ $lang->code === 'ar' ? 'ساعة ذكية,عطور,سماعات لاسلكية' : 'smart watch,perfumes,wireless headphones' }}">
                        <small class="form-text text-muted">{{ $t('messages.hero_search_animated_words_hint') ?? 'Comma-separated words that will animate in the search input box' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="hero_subtitle_{{ $lang->code }}">{{ $t('messages.hero_subtitle') ?? 'Hero Subtitle' }}</label>
                        <textarea id="hero_subtitle_{{ $lang->code }}" name="settings[{{ $lang->code }}][hero_subtitle]" class="form-control" rows="4">{{ $settingsByLocale[$lang->code]['hero_subtitle'] ?? '' }}</textarea>
                        <small class="form-text text-muted">{{ $t('messages.hero_subtitle_hint') ?? 'Main description text below the title' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="hero_search_placeholder_{{ $lang->code }}">{{ $t('messages.hero_search_placeholder') ?? 'Search Input Placeholder' }}</label>
                        <input type="text" id="hero_search_placeholder_{{ $lang->code }}" name="settings[{{ $lang->code }}][hero_search_placeholder]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['hero_search_placeholder'] ?? '' }}"
                            placeholder="{{ $lang->code === 'ar' ? 'أكتب اسم منتجك:' : 'Type your product name:' }}">
                        <small class="form-text text-muted">{{ $t('messages.hero_search_placeholder_hint') ?? 'Placeholder text for the product search input' }}</small>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="settings[{{ $lang->code }}][hero_image_enabled]" value="1"
                                {{ ($settingsByLocale[$lang->code]['hero_image_enabled'] ?? false) ? 'checked' : '' }}>
                            {{ $t('messages.enable_hero_image') ?? 'Enable Hero Image' }}
                        </label>
                        <small class="form-text text-muted">{{ $t('messages.enable_hero_image_hint') ?? 'Show the uploaded hero image instead of default illustration' }}</small>
                    </div>

                    <div class="form-group">
                        <label for="hero_video_url_{{ $lang->code }}">{{ $t('messages.hero_video_url') ?? 'Hero Video URL' }}</label>
                        <input type="url" id="hero_video_url_{{ $lang->code }}" name="settings[{{ $lang->code }}][hero_video_url]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['hero_video_url'] ?? '' }}"
                            placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                        <small class="form-text text-muted">{{ $t('messages.hero_video_url_hint') ?? 'YouTube or Vimeo video URL for the "Watch how it works" link' }}</small>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="settings[{{ $lang->code }}][hero_video_enabled]" value="1"
                                {{ ($settingsByLocale[$lang->code]['hero_video_enabled'] ?? false) ? 'checked' : '' }}>
                            {{ $t('messages.enable_hero_video') ?? 'Enable Hero Video' }}
                        </label>
                        <small class="form-text text-muted">{{ $t('messages.enable_hero_video_hint') ?? 'Show the "Watch how it works" video link' }}</small>
                    </div>
                </div>

                <!-- Platforms Section Title -->
                <div class="form-section">
                    <h3>{{ $t('messages.platforms_section') ?? 'Platforms Section' }}</h3>

                    <div class="form-group">
                        <label for="platforms_title_{{ $lang->code }}">{{ $t('messages.platforms_title') ?? 'Platforms Title' }}</label>
                        <input type="text" id="platforms_title_{{ $lang->code }}" name="settings[{{ $lang->code }}][platforms_title]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['platforms_title'] ?? '' }}"
                            placeholder="{{ $lang->code === 'ar' ? 'يدعم التكامل مع 20+ منصة' : 'Supports integration with 20+ platforms' }}">
                        <small class="form-text text-muted">{{ $t('messages.platforms_title_hint') ?? 'Title text for the supported platforms section' }}</small>
                    </div>
                </div>

                <!-- Advanced Features Section -->
                <div class="form-section">
                    <h3>{{ $t('messages.advanced_features_section') ?? 'Advanced Features Section' }}</h3>

                    <div class="form-group">
                        <label for="advanced_features_title_{{ $lang->code }}">{{ $t('messages.advanced_features_title') ?? 'Advanced Features Title' }}</label>
                        <input type="text" id="advanced_features_title_{{ $lang->code }}" name="settings[{{ $lang->code }}][advanced_features_title]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['advanced_features_title'] ?? '' }}"
                            placeholder="{{ $lang->code === 'ar' ? 'ميزات متقدمة لضمان نجاح أعمالك' : 'Advanced Features to Ensure Your Business Success' }}">
                    </div>

                    <div class="form-group">
                        <label for="advanced_features_subtitle_{{ $lang->code }}">{{ $t('messages.advanced_features_subtitle') ?? 'Advanced Features Subtitle' }}</label>
                        <textarea id="advanced_features_subtitle_{{ $lang->code }}" name="settings[{{ $lang->code }}][advanced_features_subtitle]" class="form-control" rows="3">{{ $settingsByLocale[$lang->code]['advanced_features_subtitle'] ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Basic Services Section -->
                <div class="form-section">
                    <h3>{{ $t('messages.basic_services_section') ?? 'Basic Services Section' }}</h3>

                    <div class="form-group">
                        <label for="basic_services_title_{{ $lang->code }}">{{ $t('messages.basic_services_title') ?? 'Basic Services Title' }}</label>
                        <input type="text" id="basic_services_title_{{ $lang->code }}" name="settings[{{ $lang->code }}][basic_services_title]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['basic_services_title'] ?? '' }}"
                            placeholder="{{ $lang->code === 'ar' ? 'خدماتنا الأساسية' : 'Our Basic Services' }}">
                    </div>

                    <div class="form-group">
                        <label for="basic_services_subtitle_{{ $lang->code }}">{{ $t('messages.basic_services_subtitle') ?? 'Basic Services Subtitle' }}</label>
                        <textarea id="basic_services_subtitle_{{ $lang->code }}" name="settings[{{ $lang->code }}][basic_services_subtitle]" class="form-control" rows="3">{{ $settingsByLocale[$lang->code]['basic_services_subtitle'] ?? '' }}</textarea>
                    </div>
                </div>

                <!-- FAQ Section -->
                <div class="form-section">
                    <h3>{{ $t('messages.faq_section') ?? 'FAQ Section' }}</h3>

                    <div class="form-group">
                        <label for="faq_title_{{ $lang->code }}">{{ $t('messages.faq_title') ?? 'FAQ Title' }}</label>
                        <input type="text" id="faq_title_{{ $lang->code }}" name="settings[{{ $lang->code }}][faq_title]" class="form-control"
                            value="{{ $settingsByLocale[$lang->code]['faq_title'] ?? '' }}"
                            placeholder="{{ $lang->code === 'ar' ? 'الأسئلة الشائعة' : 'Frequently Asked Questions' }}">
                    </div>

                    <div class="form-group">
                        <label for="faq_subtitle_{{ $lang->code }}">{{ $t('messages.faq_subtitle') ?? 'FAQ Subtitle' }}</label>
                        <textarea id="faq_subtitle_{{ $lang->code }}" name="settings[{{ $lang->code }}][faq_subtitle]" class="form-control" rows="3">{{ $settingsByLocale[$lang->code]['faq_subtitle'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Supported Platforms Management (Global, not per language) -->
            <div class="form-section">
                <h3>{{ $t('messages.supported_platforms') ?? 'Supported Platforms' }}</h3>
                <p class="form-text text-muted">{{ $t('messages.supported_platforms_hint') ?? 'Manage platforms that appear in the hero section. These are global settings (same for all languages).' }}</p>

                <div id="platforms-container">
                    @foreach($platforms as $index => $platform)
                    <div class="platform-item" data-platform-index="{{ $index }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label>{{ $t('messages.platform_name') ?? 'Platform Name' }}</label>
                                <input type="text" name="platforms[{{ $index }}][name]" class="form-control" value="{{ $platform['name'] ?? '' }}" required>
                            </div>
                            <div class="col-md-4">
                                <label>{{ $t('messages.platform_icon') ?? 'Icon Class' }}</label>
                                <input type="text" name="platforms[{{ $index }}][icon]" class="form-control" value="{{ $platform['icon'] ?? '' }}"
                                    placeholder="fa-brands fa-google" required>
                                <small class="form-text text-muted">{{ $t('messages.platform_icon_hint') ?? 'Font Awesome icon class (e.g., fa-brands fa-google)' }}</small>
                            </div>
                            <div class="col-md-3">
                                <label>{{ $t('messages.status') ?? 'Status' }}</label>
                                <label>
                                    <input type="checkbox" name="platforms[{{ $index }}][enabled]" value="1"
                                        {{ ($platform['enabled'] ?? true) ? 'checked' : '' }}>
                                    {{ $t('messages.enabled') ?? 'Enabled' }}
                                </label>
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm remove-platform">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-secondary" id="add-platform">
                    <i class="fa-solid fa-plus"></i> {{ $t('messages.add_platform') ?? 'Add Platform' }}
                </button>
            </div>

            <!-- Social Media Links (Global, not per language) -->
            <div class="form-section">
                <h3>{{ $t('messages.social_media_links') ?? 'Social Media Links' }}</h3>
                <p class="form-text text-muted">{{ $t('messages.social_media_links_hint') ?? 'Manage social media links that appear in the footer. These are global settings (same for all languages).' }}</p>

                <div id="social-media-container">
                    @foreach($socialMediaLinks as $index => $link)
                    <div class="social-media-item" data-social-index="{{ $index }}">
                        <div class="row">
                            <div class="col-md-4">
                                <label>{{ $t('messages.platform') ?? 'Platform' }}</label>
                                <select name="social_media[{{ $index }}][platform]" class="form-control" required>
                                    <option value="facebook" {{ ($link['platform'] ?? '') === 'facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="twitter" {{ ($link['platform'] ?? '') === 'twitter' ? 'selected' : '' }}>Twitter</option>
                                    <option value="instagram" {{ ($link['platform'] ?? '') === 'instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="linkedin" {{ ($link['platform'] ?? '') === 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                    <option value="youtube" {{ ($link['platform'] ?? '') === 'youtube' ? 'selected' : '' }}>YouTube</option>
                                    <option value="tiktok" {{ ($link['platform'] ?? '') === 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                    <option value="snapchat" {{ ($link['platform'] ?? '') === 'snapchat' ? 'selected' : '' }}>Snapchat</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>{{ $t('messages.url') ?? 'URL' }}</label>
                                <input type="url" name="social_media[{{ $index }}][url]" class="form-control" value="{{ $link['url'] ?? '' }}" placeholder="https://..." required>
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label>
                                <label>
                                    <input type="checkbox" name="social_media[{{ $index }}][enabled]" value="1" {{ ($link['enabled'] ?? true) ? 'checked' : '' }}>
                                    {{ $t('messages.enabled') ?? 'Enabled' }}
                                </label>
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-danger btn-sm remove-social-media">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-secondary" id="add-social-media">
                    <i class="fa-solid fa-plus"></i> {{ $t('messages.add_social_media') ?? 'Add Social Media' }}
                </button>
            </div>

            <div class="form-actions visible">
                <button type="submit" class="btn btn-primary">{{ $t('messages.save') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/admin-landing-page-settings.js') }}"></script>
@endpush
@endsection