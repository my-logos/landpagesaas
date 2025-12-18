<?php

namespace App\Http\Controllers\Admin;

use App\Services\SettingsService;
use App\Http\Controllers\Concerns\HandlesFileUploads;
use App\Models\Language;
use Illuminate\Http\Request;

class LandingPageSettingsController extends BaseAdminController
{
    use HandlesFileUploads;

    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function index(Request $request)
    {
        $languages = $this->getActiveLanguages();
        $settingsByLocale = $this->getSettingsByLocale($languages);
        $firstHeroImage = $this->getFirstHeroImage($languages, $settingsByLocale);
        $platforms = $this->getPlatforms();
        $socialMediaLinks = $this->getSocialMediaLinks();

        return view('admin.landing-page-settings.index', $this->getViewData(compact(
            'languages',
            'settingsByLocale',
            'firstHeroImage',
            'platforms',
            'socialMediaLinks'
        )));
    }

    public function update(Request $request)
    {
        $validated = $this->validateSettings($request);

        // Check if hero image is enabled but no image exists
        $this->validateHeroImage($request);

        $this->handleHeroImageUpload($request);
        $this->saveSettings($validated['settings']);
        $this->savePlatforms($request->input('platforms', []));
        $this->saveSocialMediaLinks($request->input('social_media', []));

        return $this->redirectWithSuccess(
            'admin.landing-page-settings.index',
            'messages.settings_updated',
            'Landing page settings updated successfully'
        );
    }

    /**
     * Get active languages with fallback
     */
    private function getActiveLanguages()
    {
        $languages = Language::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        if ($languages->isEmpty()) {
            return collect([
                (object)['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr'],
                (object)['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'direction' => 'rtl'],
            ]);
        }

        return $languages;
    }

    /**
     * Get settings grouped by locale
     */
    private function getSettingsByLocale($languages): array
    {
        $settingKeys = [
            'hero_title_prefix',
            'hero_animated_words',
            'hero_search_animated_words',
            'hero_subtitle',
            'hero_search_placeholder',
            'hero_video_url',
            'hero_video_enabled',
            'hero_image',
            'hero_image_enabled',
            'platforms_title',
            'advanced_features_title',
            'advanced_features_subtitle',
            'advanced_features_content',
            'basic_services_title',
            'basic_services_subtitle',
            'basic_services_content',
            'faq_title',
            'faq_subtitle',
            'faq_content',
            'social_media_links',
        ];

        $settingsByLocale = [];

        foreach ($languages as $lang) {
            $settingsByLocale[$lang->code] = [];

            foreach ($settingKeys as $key) {
                $default = $this->getDefaultValue($key, $lang->code);
                $settingsByLocale[$lang->code][$key] = $this->settingsService->get($key, $default, $lang->code);
            }
        }

        return $settingsByLocale;
    }

    /**
     * Get default values for settings
     */
    private function getDefaultValue(string $key, string $locale): string
    {
        $defaults = [
            'en' => [
                'hero_title_prefix' => 'Create',
                'hero_animated_words' => 'smart watch,digital product,game',
                'hero_search_animated_words' => 'smart watch,perfumes,wireless headphones',
                'hero_subtitle' => 'In 30 seconds ⚡ with AI. Double your profits with DropSaas. Fast hosting, professional design with a click of a button, and advanced protection that saves 100% of your wasted advertising budget.',
                'hero_search_placeholder' => 'Type your product name:',
                'platforms_title' => 'Supports integration with 20+ platforms',
                'advanced_features_title' => 'Advanced Features to Ensure Your Business Success',
                'advanced_features_subtitle' => 'We do not just offer landing pages, but rather an integrated system to protect your investments and increase your profits in a guaranteed way',
                'basic_services_title' => 'Our Basic Services',
                'basic_services_subtitle' => 'We provide you with advanced tools to create attractive and effective landing pages that help you increase your sales',
                'faq_title' => 'Frequently Asked Questions',
                'faq_subtitle' => 'Answers to the most common questions about our platform and services',
            ],
            'ar' => [
                'hero_title_prefix' => 'أنشئ',
                'hero_animated_words' => 'ساعة ذكية,منتج رقمي,لعبة',
                'hero_search_animated_words' => 'ساعة ذكية,عطور,سماعات لاسلكية',
                'hero_subtitle' => 'في 30 ثانية ⚡ بالذكاء الاصطناعي. ضاعف أرباحك مع DropSaas. استضافة سريعة، تصميم احترافي بضغطة زر, وحماية متقدمة توفر 100% من ميزانية إعلاناتك المهدرة.',
                'hero_search_placeholder' => 'أكتب اسم منتجك:',
                'platforms_title' => 'يدعم التكامل مع 20+ منصة',
                'advanced_features_title' => 'ميزات متقدمة لضمان نجاح أعمالك',
                'advanced_features_subtitle' => 'نحن لا نقدم مجرد صفحات هبوط بل نقدم نظاماً متكاملاً لحماية استثماراتك وزيادة أرباحك بشكل مضمون',
                'basic_services_title' => 'خدماتنا الأساسية',
                'basic_services_subtitle' => 'تقدم لك أدوات متقدمة لإنشاء صفحات هبوط جذابة وفعالة تساعدك على زيادة مبيعاتك',
                'faq_title' => 'الأسئلة الشائعة',
                'faq_subtitle' => 'إجابات على الأسئلة الأكثر شيوعًا حول منصتنا وخدماتنا',
            ],
        ];

        return $defaults[$locale][$key] ?? '';
    }

    /**
     * Get first available hero image
     */
    private function getFirstHeroImage($languages, $settingsByLocale): ?string
    {
        foreach ($languages as $lang) {
            if (!empty($settingsByLocale[$lang->code]['hero_image'])) {
                return $settingsByLocale[$lang->code]['hero_image'];
            }
        }
        return null;
    }

    /**
     * Get social media links from settings
     */
    private function getSocialMediaLinks(): array
    {
        $linksJson = $this->settingsService->get('social_media_links', '[]');
        $links = json_decode($linksJson, true);

        if (!is_array($links) || empty($links)) {
            return [
                ['platform' => 'facebook', 'url' => '#', 'enabled' => true],
                ['platform' => 'twitter', 'url' => '#', 'enabled' => true],
                ['platform' => 'instagram', 'url' => '#', 'enabled' => true],
                ['platform' => 'linkedin', 'url' => '#', 'enabled' => true],
                ['platform' => 'youtube', 'url' => '#', 'enabled' => true],
            ];
        }

        return $links;
    }

    /**
     * Get platforms from settings
     */
    private function getPlatforms(): array
    {
        $platformsJson = $this->settingsService->get('landing_platforms', '[]');
        $platforms = json_decode($platformsJson, true);

        if (!is_array($platforms) || empty($platforms)) {
            // Default platforms
            return [
                ['name' => 'Google Analytics', 'icon' => 'fa-brands fa-google', 'enabled' => true],
                ['name' => 'Webhooks', 'icon' => 'fa-solid fa-link', 'enabled' => true],
                ['name' => 'Mailchimp', 'icon' => 'fa-brands fa-mailchimp', 'enabled' => true],
                ['name' => 'Zapier', 'icon' => 'fa-brands fa-zapier', 'enabled' => true],
                ['name' => 'Facebook Pixel', 'icon' => 'fa-brands fa-facebook', 'enabled' => true],
                ['name' => 'TikTok Ads', 'icon' => 'fa-brands fa-tiktok', 'enabled' => true],
                ['name' => 'Snapchat', 'icon' => 'fa-brands fa-snapchat', 'enabled' => true],
                ['name' => 'Google Sheets', 'icon' => 'fa-solid fa-table', 'enabled' => true],
                ['name' => 'WhatsApp', 'icon' => 'fa-brands fa-whatsapp', 'enabled' => true],
                ['name' => 'Taager', 'icon' => 'fa-solid fa-cart-shopping', 'enabled' => true],
                ['name' => 'Mylerz', 'icon' => 'fa-solid fa-box', 'enabled' => true],
            ];
        }

        return $platforms;
    }

    /**
     * Validate settings request
     */
    private function validateSettings(Request $request): array
    {
        return $request->validate([
            'settings' => 'required|array',
            'settings.*.locale' => 'required|string',
            'settings.*.hero_title_prefix' => 'nullable|string|max:255',
            'settings.*.hero_animated_words' => 'nullable|string|max:500',
            'settings.*.hero_search_animated_words' => 'nullable|string|max:500',
            'settings.*.hero_subtitle' => 'nullable|string|max:1000',
            'settings.*.hero_search_placeholder' => 'nullable|string|max:255',
            'settings.*.hero_video_url' => 'nullable|url|max:500',
            'settings.*.hero_video_enabled' => 'nullable',
            'settings.*.hero_image_enabled' => 'nullable',
            'settings.*.platforms_title' => 'nullable|string|max:255',
            'settings.*.advanced_features_title' => 'nullable|string|max:255',
            'settings.*.advanced_features_subtitle' => 'nullable|string|max:1000',
            'settings.*.advanced_features_content' => 'nullable|string',
            'settings.*.basic_services_title' => 'nullable|string|max:255',
            'settings.*.basic_services_subtitle' => 'nullable|string|max:1000',
            'settings.*.basic_services_content' => 'nullable|string',
            'settings.*.faq_title' => 'nullable|string|max:255',
            'settings.*.faq_subtitle' => 'nullable|string|max:1000',
            'settings.*.faq_content' => 'nullable|string',
            'platforms' => 'nullable|array',
            'social_media' => 'nullable|array',
            'social_media.*.platform' => 'required_with:social_media|string|max:50',
            'social_media.*.url' => 'required_with:social_media|url|max:500',
            'social_media.*.enabled' => 'nullable',
            'platforms.*.name' => 'required_with:platforms|string|max:255',
            'platforms.*.icon' => 'required_with:platforms|string|max:255',
            'platforms.*.enabled' => 'nullable',
        ]);
    }

    /**
     * Validate hero image requirement
     */
    private function validateHeroImage(Request $request): void
    {
        $settings = $request->input('settings', []);
        $hasHeroImageEnabled = false;
        $hasHeroImage = false;

        // Check if any language has hero_image_enabled
        foreach ($settings as $localeData) {
            if (isset($localeData['hero_image_enabled']) && $localeData['hero_image_enabled']) {
                $hasHeroImageEnabled = true;
                break;
            }
        }

        // Check if hero image exists (file upload or existing URL)
        if ($request->hasFile('hero_image')) {
            $hasHeroImage = true;
        } elseif ($request->input('hero_image_url')) {
            $hasHeroImage = true;
        } else {
            // Check if hero image exists in settings
            $existingImage = $this->settingsService->get('hero_image');
            if (!empty($existingImage)) {
                $hasHeroImage = true;
            }
        }

        // If hero image is enabled but no image exists, require upload
        if ($hasHeroImageEnabled && !$hasHeroImage) {
            $request->validate([
                'hero_image' => 'required|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            ], [
                'hero_image.required' => \App\Helpers\TranslationHelper::get(
                    'messages.hero_image_required',
                    app()->getLocale() === 'ar'
                        ? 'يجب رفع صورة البطل عند تفعيل عرض الصورة'
                        : 'Hero image is required when hero image is enabled'
                ),
                'hero_image.mimes' => app()->getLocale() === 'ar'
                    ? 'يجب أن يكون الملف من نوع: jpeg, png, jpg, gif, webp, svg'
                    : 'The file must be of type: jpeg, png, jpg, gif, webp, svg',
                'hero_image.max' => app()->getLocale() === 'ar'
                    ? 'حجم الصورة يجب أن يكون أقل من 5 ميجابايت'
                    : 'Image size must be less than 5MB',
            ]);
        }
    }

    /**
     * Handle hero image upload
     */
    private function handleHeroImageUpload(Request $request): void
    {
        $imageUrl = $this->getHeroImageUrl($request);

        if ($imageUrl) {
            $this->saveHeroImageForAllLanguages($imageUrl);
        }
    }

    /**
     * Get hero image URL from request (file upload or URL input)
     */
    private function getHeroImageUrl(Request $request): ?string
    {
        if ($request->hasFile('hero_image')) {
            $imageName = $this->uploadImage($request, 'hero_image', 'hero-images', 'public', 5120);
            return $imageName ? '/storage/hero-images/' . $imageName : null;
        }

        return $request->input('hero_image_url');
    }

    /**
     * Save hero image URL for all active languages
     */
    private function saveHeroImageForAllLanguages(string $imageUrl): void
    {
        $languages = Language::where('is_active', true)->get();
        foreach ($languages as $lang) {
            $this->settingsService->set('hero_image', $imageUrl, $lang->code);
        }
    }

    /**
     * Save settings for all locales
     */
    private function saveSettings(array $settingsData): void
    {
        foreach ($settingsData as $localeData) {
            $locale = $localeData['locale'];
            foreach ($localeData as $key => $value) {
                if ($key !== 'locale') {
                    // Convert checkbox values to booleans
                    if (in_array($key, ['hero_video_enabled', 'hero_image_enabled'])) {
                        $value = $value ? true : false;
                    }
                    $this->settingsService->set($key, $value, $locale);
                }
            }
        }
    }

    /**
     * Save platforms (global, not per locale)
     * Save to 'en' locale as default for global settings, and also to current locale
     */
    private function savePlatforms(array $platforms): void
    {
        // Filter out empty platforms and normalize enabled status
        $platforms = array_filter($platforms, function ($platform) {
            return !empty($platform['name']) && !empty($platform['icon']);
        });

        // Normalize enabled status (checkbox sends '1' if checked, nothing if unchecked)
        $platforms = array_map(function ($platform) {
            $platform['enabled'] = isset($platform['enabled']) && $platform['enabled'] !== false && $platform['enabled'] !== '0';
            return $platform;
        }, $platforms);

        // Reset array keys
        $platforms = array_values($platforms);

        // Save to 'en' locale as default for global settings
        // Also save to current locale to ensure it's available
        $this->settingsService->set('landing_platforms', json_encode($platforms), 'en');
        $this->settingsService->set('landing_platforms', json_encode($platforms), app()->getLocale());
    }

    /**
     * Save social media links (global, not per locale)
     * Save to 'en' locale as default for global settings, and also to current locale
     */
    private function saveSocialMediaLinks(array $links): void
    {
        // Filter out empty links and normalize enabled status
        $links = array_filter($links, function ($link) {
            return !empty($link['platform']) && !empty($link['url']);
        });

        // Normalize enabled status (checkbox sends '1' if checked, nothing if unchecked)
        $links = array_map(function ($link) {
            $link['enabled'] = isset($link['enabled']) && $link['enabled'] !== false && $link['enabled'] !== '0';
            return $link;
        }, $links);

        // Reset array keys
        $links = array_values($links);

        // Save to 'en' locale as default for global settings
        // Also save to current locale to ensure it's available
        $this->settingsService->set('social_media_links', json_encode($links), 'en');
        $this->settingsService->set('social_media_links', json_encode($links), app()->getLocale());
    }
}
