<?php

namespace App\Http\Controllers;

use App\Services\LandingPageService;
use App\Services\SettingsService;
use App\View\Composers\LandingPageComposer;

class LandingPageController extends Controller
{
    public function __construct(
        protected LandingPageService $landingPageService,
        protected SettingsService $settingsService,
        protected LandingPageComposer $composer
    ) {}

    public function index()
    {
        $view = view('landing');
        $this->composer->compose($view);

        $locale = app()->getLocale();
        $landingSettings = $this->getLandingPageSettings($locale);
        $platforms = $this->getPlatforms();

        // Get social media links (global - try all locales)
        $socialMediaLinks = $this->getSocialMediaLinks();

        $view->with(array_merge([
            'packages' => $this->landingPageService->getPackages(),
            'statistics' => $this->landingPageService->getStatistics(),
            'siteLogo' => $this->settingsService->get('site_logo'),
            'siteName' => $this->settingsService->get('site_name', 'DropSaas'),
            'platforms' => $platforms,
            'socialMediaLinks' => $socialMediaLinks,
        ], $landingSettings));

        return $view;
    }

    /**
     * Get landing page settings for a locale
     */
    private function getLandingPageSettings(string $locale): array
    {
        $defaults = $this->getDefaultSettings($locale);

        return [
            'heroTitlePrefix' => $this->settingsService->get('hero_title_prefix', $defaults['hero_title_prefix'], $locale),
            'heroAnimatedWords' => $this->settingsService->get('hero_animated_words', $defaults['hero_animated_words'], $locale),
            'heroSearchAnimatedWords' => $this->settingsService->get('hero_search_animated_words', $defaults['hero_search_animated_words'], $locale),
            'heroSubtitle' => $this->settingsService->get('hero_subtitle', $defaults['hero_subtitle'], $locale),
            'heroSearchPlaceholder' => $this->settingsService->get('hero_search_placeholder', $defaults['hero_search_placeholder'], $locale),
            'heroVideoUrl' => $this->settingsService->get('hero_video_url', '', $locale),
            'heroVideoEnabled' => $this->settingsService->get('hero_video_enabled', false, $locale),
            'heroImage' => $this->settingsService->get('hero_image', '', $locale),
            'heroImageEnabled' => $this->settingsService->get('hero_image_enabled', false, $locale),
            'platformsTitle' => $this->settingsService->get('platforms_title', $defaults['platforms_title'], $locale),
            'advancedFeaturesTitle' => $this->settingsService->get('advanced_features_title', $defaults['advanced_features_title'], $locale),
            'advancedFeaturesSubtitle' => $this->settingsService->get('advanced_features_subtitle', $defaults['advanced_features_subtitle'], $locale),
            'basicServicesTitle' => $this->settingsService->get('basic_services_title', $defaults['basic_services_title'], $locale),
            'basicServicesSubtitle' => $this->settingsService->get('basic_services_subtitle', $defaults['basic_services_subtitle'], $locale),
            'faqTitle' => $this->settingsService->get('faq_title', $defaults['faq_title'], $locale),
            'faqSubtitle' => $this->settingsService->get('faq_subtitle', $defaults['faq_subtitle'], $locale),
        ];
    }

    /**
     * Get default settings for a locale
     */
    private function getDefaultSettings(string $locale): array
    {
        if ($locale === 'ar') {
            return [
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
            ];
        }

        return [
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
        ];
    }

    /**
     * Get platforms list from any available locale (global setting)
     */
    private function getPlatforms(): array
    {
        // Try to get from current locale first
        $platformsJson = $this->settingsService->get('landing_platforms', null);

        // If not found, try to get from any locale
        if ($platformsJson === null) {
            $setting = \App\Models\Settings::where('key', 'landing_platforms')
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($setting) {
                $platformsJson = $setting->value;
            }
        }

        // Decode JSON
        $platforms = [];
        if ($platformsJson) {
            $decoded = json_decode($platformsJson, true);
            if (is_array($decoded)) {
                $platforms = $decoded;
            }
        }

        // If still empty, use defaults
        if (empty($platforms)) {
            return $this->getDefaultPlatforms();
        }

        // Filter only enabled platforms and normalize enabled status
        $platforms = array_filter($platforms, function ($platform) {
            $enabled = $platform['enabled'] ?? true;
            // Handle string '1' or boolean true
            if (is_string($enabled)) {
                $enabled = $enabled === '1' || $enabled === 'true';
            }
            return $enabled === true;
        });

        return array_values($platforms);
    }

    /**
     * Get default platforms
     */
    private function getDefaultPlatforms(): array
    {
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

    /**
     * Get social media links from any available locale (global setting)
     */
    private function getSocialMediaLinks(): array
    {
        // Try to get from current locale first
        $socialMediaJson = $this->settingsService->get('social_media_links', null);

        // If not found, try to get from any locale
        if ($socialMediaJson === null) {
            $setting = \App\Models\Settings::where('key', 'social_media_links')
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($setting) {
                $socialMediaJson = $setting->value;
            }
        }

        // Decode JSON
        $socialMediaLinks = [];
        if ($socialMediaJson) {
            $decoded = json_decode($socialMediaJson, true);
            if (is_array($decoded)) {
                $socialMediaLinks = $decoded;
            }
        }

        // If still empty, use defaults
        if (empty($socialMediaLinks)) {
            $socialMediaLinks = [
                ['platform' => 'facebook', 'url' => '#', 'enabled' => true],
                ['platform' => 'twitter', 'url' => '#', 'enabled' => true],
                ['platform' => 'instagram', 'url' => '#', 'enabled' => true],
                ['platform' => 'linkedin', 'url' => '#', 'enabled' => true],
                ['platform' => 'youtube', 'url' => '#', 'enabled' => true],
            ];
        }

        // Filter only enabled links and normalize enabled status
        $socialMediaLinks = array_filter($socialMediaLinks, function ($link) {
            $enabled = $link['enabled'] ?? true;
            // Handle string '1' or boolean true
            if (is_string($enabled)) {
                $enabled = $enabled === '1' || $enabled === 'true';
            }
            return $enabled === true;
        });

        return array_values($socialMediaLinks);
    }
}
