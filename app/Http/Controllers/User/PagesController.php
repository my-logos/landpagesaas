<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\EnforcesPackageLimits;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\HasSubscriptionHelper;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Product;
use App\Models\LandingPageTemplate;
use Illuminate\Http\Request;
use App\Helpers\TranslationHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PagesController extends Controller
{
    use EnforcesPackageLimits, HasLocaleAndTranslation, HasSubscriptionHelper;

    public function index(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $query = Page::where('user_id', $user->id)->with(['product', 'template']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $pages = $query->latest()->get();

        // Ensure all pages have slugs and fix old slugs with random numbers
        foreach ($pages as $page) {
            if (empty($page->slug)) {
                $this->generateSlugForPage($page);
            } else {
                // Fix old slugs that have random numbers (like test-1765800501-20)
                // Check if slug ends with pattern: -number-number (where first number is random timestamp)
                if (preg_match('/^(.+)-(\d{10,})-(\d+)$/', $page->slug, $matches)) {
                    // Regenerate slug from title to ensure consistency
                    $englishTitle = $this->convertToEnglish($page->title ?? 'page');
                    $newBaseSlug = Str::slug($englishTitle);

                    if (empty($newBaseSlug)) {
                        $newBaseSlug = 'page';
                    }

                    // Update slug to: base-slug-id (no random numbers)
                    $page->update([
                        'slug' => $newBaseSlug . '-' . $page->id
                    ]);
                    $page->refresh();
                } elseif (preg_match('/^(.+)-temp$/', $page->slug)) {
                    // Fix temp slugs
                    $this->updatePageSlug($page);
                }
            }
        }

        // Get subscription data
        $subscriptionData = $this->getSubscriptionData($user);
        $additionalSalesEnabled = $user->additional_sales_enabled ?? false;

        // Calculate progress steps
        $hasPages = $pages->count() > 0;
        $hasPublishedPages = $pages->where('status', 'published')->count() > 0;
        $currentPackage = $subscriptionData['package'] ?? null;
        $isFreePlan = !$currentPackage || ($currentPackage && ($currentPackage->is_free || $currentPackage->name === 'Free'));

        // Get package features for enabling/disabling pixel fields in modals
        $packageFeatures = [
            'facebook_pixel' => $currentPackage ? $currentPackage->hasFeature('facebook_pixel') : false,
            'tiktok_pixel' => $currentPackage ? $currentPackage->hasFeature('tiktok_pixel') : false,
            'snapchat_pixel' => $currentPackage ? $currentPackage->hasFeature('snapchat_pixel') : false,
            'google_analytics' => $currentPackage ? $currentPackage->hasFeature('google_analytics') : false,
        ];

        $completedSteps = 0;
        if ($hasPages) $completedSteps++;
        if ($hasPublishedPages) $completedSteps++;
        if (!$isFreePlan) $completedSteps++;

        $progressSteps = [
            'step1' => [
                'completed' => $hasPages,
                'title' => $locale === 'ar' ? 'إنشاء صفحة هبوط' : 'Create Landing Page',
                'description' => $locale === 'ar' ? 'قم بإنشاء أول منتج وصفحة هبوط احترافية باستخدام الذكاء الاصطناعي في ثوان.' : 'Create your first professional product and landing page using AI in seconds.',
                'action_text' => $locale === 'ar' ? 'إنشاء صفحة الآن' : 'Create Page Now',
                'action_url' => route('user.pages.create')
            ],
            'step2' => [
                'completed' => $hasPublishedPages,
                'title' => $locale === 'ar' ? 'نشر الصفحة' : 'Publish Page',
                'description' => $locale === 'ar' ? 'قم بنشر صفحتك لتصبح متاحة للزوار. انقر على زر "نشر" في قائمة الإجراءات لصفحتك.' : 'Publish your page to make it available to visitors. Click the "Publish" button in your page\'s actions menu.',
            ],
            'step3' => [
                'completed' => !$isFreePlan,
                'title' => $locale === 'ar' ? 'الترقية للاستمرار بلا توقف' : 'Upgrade for Continuous Operation',
                'description' => $locale === 'ar' ? 'اشترك في باقة مدفوعة لضمان بقاء صفحاتك منشورة، تفعيل الميزات المتقدمة (Pixel, Webhooks)، وتجنب توقف الخدمة.' : 'Subscribe to a paid plan to ensure your pages remain published, activate advanced features (Pixel, Webhooks), and avoid service interruption.',
                'action_text' => $locale === 'ar' ? 'عرض الخطط' : 'View Plans',
                'action_url' => route('user.packages.index')
            ]
        ];

        // Get published pages for store links modal
        $publishedPages = $pages->where('status', 'published');

        return view('user.pages.index', compact(
            'pages',
            'publishedPages',
            'locale',
            'dir',
            't',
            'subscriptionData',
            'additionalSalesEnabled',
            'progressSteps',
            'completedSteps',
            'isFreePlan',
            'packageFeatures'
        ));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $products = Product::where('user_id', $user->id)->get();
        $templates = LandingPageTemplate::getEnabled();
        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'] ?? null;

        // Pre-select product if provided in query string
        $selectedProductId = $request->get('product_id');

        // Check if AI is available
        $aiService = app(\App\Services\AIService::class);
        $aiAvailable = $aiService->isAvailable();

        // Get package features for enabling/disabling pixel fields
        $packageFeatures = [
            'facebook_pixel' => $currentPackage ? $currentPackage->hasFeature('facebook_pixel') : false,
            'tiktok_pixel' => $currentPackage ? $currentPackage->hasFeature('tiktok_pixel') : false,
            'snapchat_pixel' => $currentPackage ? $currentPackage->hasFeature('snapchat_pixel') : false,
            'google_analytics' => $currentPackage ? $currentPackage->hasFeature('google_analytics') : false,
        ];

        // Check if free plan
        $isFreePlan = !$currentPackage || ($currentPackage && ($currentPackage->is_free || $currentPackage->name === 'Free'));

        // Determine page type for edit mode
        $pageType = 'classic';
        $isEditMode = false;
        if (isset($page)) {
            $isEditMode = true;
            if ($page->ai_version) {
                $pageType = 'ai';
            } else {
                $pageType = 'classic';
            }
        }

        // AI enabled status
        $aiEnabled = !$isFreePlan && $aiAvailable;

        // Get sizes and colors for variations
        $sizes = [];
        $colors = [];
        if (isset($page) && $page->settings) {
            $settings = $page->settings;
            $sizes = $settings['sizes'] ?? [];
            $colors = $settings['colors'] ?? [];
        }

        // Get form data for edit mode
        $currentLanguage = old('language');
        if (isset($page) && $page->content && !$currentLanguage) {
            $content = is_string($page->content) ? json_decode($page->content, true) : $page->content;
            $currentLanguage = $content['language'] ?? null;
        }

        $formType = old('form_type', isset($page) ? $page->form_type : 'default');
        $formFields = old('form_fields', isset($page) && $page->form_fields ? json_encode($page->form_fields) : '[]');

        return view('user.pages.form', compact(
            'products',
            'selectedProductId',
            'templates',
            'locale',
            'dir',
            't',
            'aiAvailable',
            'aiEnabled',
            'packageFeatures',
            'isFreePlan',
            'pageType',
            'isEditMode',
            'sizes',
            'colors',
            'currentLanguage',
            'formType',
            'formFields'
        ));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Check limits
        $count = Page::where('user_id', $user->id)->count();

        // Check limit and return redirect with flash message if limit reached
        $limitCheck = $this->checkLimitWithRedirect($user, 'pages_limit', $count, route('user.pages.index'));
        if ($limitCheck) {
            return $limitCheck;
        }

        $creationType = $request->input('creation_type', 'classic'); // Default to Classic

        // Check if user has paid plan for AI feature
        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'] ?? null;
        $isFreePlan = !$currentPackage || ($currentPackage && ($currentPackage->is_free || $currentPackage->name === 'Free'));

        // If AI creation requested but user is on free plan, reject and redirect to classic
        if ($creationType === 'ai' && $isFreePlan) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->getTranslatedMessage('messages.ai_requires_paid_plan', 'AI feature is only available for paid plans. Please upgrade to use this feature.'));
        }

        if ($creationType === 'classic') {
            // Convert form_fields from JSON string to array before validation
            $requestData = $request->all();
            if (isset($requestData['form_fields']) && is_string($requestData['form_fields'])) {
                $requestData['form_fields'] = json_decode($requestData['form_fields'], true) ?? [];
                $request->merge($requestData);
            }

            // Classic creation - validate classic fields
            $validated = $this->validatePageData($request);

            // Verify product ownership if provided
            $product = null;
            if (!empty($validated['product_id'])) {
                $product = Product::findOrFail($validated['product_id']);
                $this->validateProductOwnership($validated['product_id'], $user);
            }

            // Process form fields
            $validated = $this->processFormFields($validated);

            // Process sizes and colors
            $sizes = $this->processVariations($request->input('sizes', []), false);
            $colors = $this->processVariations($request->input('colors', []), true);

            // Prepare settings with sizes and colors
            $settings = [
                'sizes' => $sizes,
                'colors' => $colors,
            ];

            // Generate English-only slug from title
            $englishTitle = $this->convertToEnglish($validated['title']);
            $baseSlug = Str::slug($englishTitle);

            // If slug is empty, use default
            if (empty($baseSlug)) {
                $baseSlug = 'page';
            }

            // Use temporary slug (will be updated with actual ID after creation)
            $slug = $baseSlug . '-temp';

            // Prepare page data
            $data = $this->preparePageData(array_merge($validated, [
                'user_id' => $user->id,
                'slug' => $slug,
                'status' => 'draft',
                'settings' => $settings,
            ]), $user);

            // Get language from request (required field now)
            $language = $validated['language'] ?? app()->getLocale();

            // If product exists, generate AI content for description, features, and FAQs
            $aiContent = null;
            if ($product) {
                $aiService = app(\App\Services\PageGenerationService::class);

                // Generate AI content
                $aiResult = $aiService->generateClassicPageContent($product, $language);

                if ($aiResult['success'] && isset($aiResult['content'])) {
                    // Store AI-generated content as JSON in the content field
                    $aiContent = $aiResult['content'];
                    // Ensure language is set in content
                    $aiContent['language'] = $language;
                    $data['content'] = json_encode($aiContent);
                } else {
                    // If AI generation fails, log error but continue without AI content
                    Log::warning('Failed to generate AI content for classic page', [
                        'product_id' => $product->id,
                        'error' => $aiResult['message'] ?? 'Unknown error'
                    ]);
                    // Store language even if AI fails
                    $data['content'] = json_encode(['language' => $language]);
                }
            } else {
                // No product, just store language in content
                $data['content'] = json_encode(['language' => $language]);
            }

            // Apply package limits
            $data = $this->applyPackageLimits($data, $user);

            // Create the page
            $page = Page::create($data);

            // Update slug with actual page ID
            $this->updatePageSlug($page);

            return redirect()->route('user.pages.index')
                ->with('success', $this->getTranslatedMessage('messages.page_created', 'Page created successfully'));
        }

        // AI creation - validate AI fields
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'language' => 'required|in:ar,en',
            'api_key' => 'nullable|string', // Optional API key for testing
        ]);

        // Verify product ownership
        $product = Product::findOrFail($validated['product_id']);
        $this->validateProductOwnership($product->id, $user);

        // Get AI service to check availability
        $aiService = app(\App\Services\AIService::class);

        // Check if API key is provided for testing, otherwise use configured one
        $apiKey = $validated['api_key'] ?? null;
        if (!$apiKey && !$aiService->isAvailable()) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->getTranslatedMessage('messages.ai_not_configured', 'AI service is not configured. Please contact administrator.'));
        }

        // Prepare page data for AI generation
        $shippingPrice = $product->shipping_price_cents ? ($product->shipping_price_cents / 100) : 15.0;

        // Convert form_fields from JSON string to array if needed
        $formFields = $request->input('form_fields', []);
        if (is_string($formFields)) {
            $formFields = json_decode($formFields, true) ?? [];
        }
        if (!is_array($formFields)) {
            $formFields = [];
        }

        $pageData = [
            'title' => $product->name,
            'additional_description' => $product->description ?? '',
            'seo_title' => $product->name,
            'seo_description' => $product->short_description ?? $product->description ?? '',
            'seo_keywords' => '',
            'form_type' => $request->input('form_type', 'default'),
            'form_fields' => $formFields,
            'shipping_price' => $shippingPrice,
            'template_id' => null,
        ];

        // Generate English-only slug from product name
        $englishName = $this->convertToEnglish($product->name);
        $baseSlug = Str::slug($englishName);

        // If slug is empty, use default
        if (empty($baseSlug)) {
            $baseSlug = 'page';
        }

        // Use temporary slug (will be updated with actual ID after creation)
        $slug = $baseSlug . '-temp';

        // Prepare page data for database (create page with pending status)
        $data = $this->preparePageData([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'title' => $product->name,
            'slug' => $slug,
            'status' => 'draft',
            'generation_status' => 'pending',
            'seo_title' => $product->name,
            'seo_description' => $product->short_description ?? $product->description ?? '',
            'seo_keywords' => '',
            'form_type' => $pageData['form_type'],
            'form_fields' => $pageData['form_fields'],
            'ai_version' => 'v3',
        ], $user);

        // Apply package limits
        $data = $this->applyPackageLimits($data, $user);

        // Create the page with pending status
        $page = Page::create($data);

        // Update slug with actual page ID
        $this->updatePageSlug($page);

        // Dispatch job to generate page in background
        \App\Jobs\GeneratePageJob::dispatch($page, $product, $pageData, $validated['language'], $apiKey);

        return redirect()->route('user.pages.index')
            ->with('success', $this->getTranslatedMessage('messages.page_creation_started', 'Page creation started. It will be ready shortly.'));
    }

    /**
     * Show the form for editing a page
     */
    public function edit(Request $request, Page $page)
    {
        $user = $request->user();

        // Verify ownership
        $this->verifyPageOwnership($page, $user);

        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $products = Product::where('user_id', $user->id)->get();
        $templates = LandingPageTemplate::getEnabled();
        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'] ?? null;

        // Check if AI is available
        $aiService = app(\App\Services\AIService::class);
        $aiAvailable = $aiService->isAvailable();

        // Get package features for enabling/disabling pixel fields
        $packageFeatures = [
            'facebook_pixel' => $currentPackage ? $currentPackage->hasFeature('facebook_pixel') : false,
            'tiktok_pixel' => $currentPackage ? $currentPackage->hasFeature('tiktok_pixel') : false,
            'snapchat_pixel' => $currentPackage ? $currentPackage->hasFeature('snapchat_pixel') : false,
            'google_analytics' => $currentPackage ? $currentPackage->hasFeature('google_analytics') : false,
        ];

        // Check if free plan
        $isFreePlan = !$currentPackage || ($currentPackage && ($currentPackage->is_free || $currentPackage->name === 'Free'));

        // Determine page type for edit mode
        $pageType = 'classic';
        $isEditMode = true;
        if ($page->ai_version) {
            $pageType = 'ai';
        } else {
            $pageType = 'classic';
        }

        // AI enabled status
        $aiEnabled = !$isFreePlan && $aiAvailable;

        // Get sizes and colors for variations
        $sizes = [];
        $colors = [];
        if ($page->settings) {
            $settings = $page->settings;
            $sizes = $settings['sizes'] ?? [];
            $colors = $settings['colors'] ?? [];
        }

        // Get form data for edit mode
        $currentLanguage = old('language');
        if ($page->content && !$currentLanguage) {
            $content = is_string($page->content) ? json_decode($page->content, true) : $page->content;
            $currentLanguage = $content['language'] ?? null;
        }

        $formType = old('form_type', $page->form_type ?? 'default');
        $formFields = old('form_fields', $page->form_fields ? json_encode($page->form_fields) : '[]');

        // Pre-select product if page has one
        $selectedProductId = $page->product_id ?? null;

        return view('user.pages.form', compact(
            'page',
            'products',
            'selectedProductId',
            'templates',
            'locale',
            'dir',
            't',
            'aiAvailable',
            'aiEnabled',
            'packageFeatures',
            'isFreePlan',
            'pageType',
            'isEditMode',
            'sizes',
            'colors',
            'currentLanguage',
            'formType',
            'formFields'
        ));
    }

    /**
     * Update a page
     */
    public function update(Request $request, Page $page)
    {
        $user = $request->user();

        // Verify ownership
        $this->verifyPageOwnership($page, $user);

        // Check limits
        $count = Page::where('user_id', $user->id)->where('id', '!=', $page->id)->count();
        $this->enforceLimit($user, 'pages_limit', $count);

        $creationType = $request->input('creation_type', 'classic');

        // Check if user has paid plan for AI feature
        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'] ?? null;
        $isFreePlan = !$currentPackage || ($currentPackage && ($currentPackage->is_free || $currentPackage->name === 'Free'));

        // If AI creation requested but user is on free plan, reject
        if ($creationType === 'ai' && $isFreePlan) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->getTranslatedMessage('messages.ai_requires_paid_plan', 'AI feature is only available for paid plans. Please upgrade to use this feature.'));
        }

        if ($creationType === 'classic') {
            // Convert form_fields from JSON string to array before validation
            $requestData = $request->all();
            if (isset($requestData['form_fields']) && is_string($requestData['form_fields'])) {
                $requestData['form_fields'] = json_decode($requestData['form_fields'], true) ?? [];
                $request->merge($requestData);
            }

            // Classic update - validate classic fields
            $validated = $this->validatePageData($request);

            // Verify product ownership if provided
            if (!empty($validated['product_id'])) {
                $this->validateProductOwnership($validated['product_id'], $user);
            }

            // Process form fields
            $validated = $this->processFormFields($validated);

            // Process sizes and colors
            $sizes = $this->processVariations($request->input('sizes', []), false);
            $colors = $this->processVariations($request->input('colors', []), true);

            // Get current settings and merge with sizes and colors
            $currentSettings = $page->settings ?? [];
            $currentSettings['sizes'] = $sizes;
            $currentSettings['colors'] = $colors;

            // Get language from request (required field now)
            $language = $validated['language'] ?? app()->getLocale();

            // Get current content or initialize
            $currentContent = $page->content ? (is_string($page->content) ? json_decode($page->content, true) : $page->content) : [];
            if (!is_array($currentContent)) {
                $currentContent = [];
            }

            // Get product if provided (already validated ownership above)
            $product = null;
            if (!empty($validated['product_id'])) {
                $product = Product::find($validated['product_id']);
            }

            // If product exists, regenerate AI content
            if ($product) {
                $aiService = app(\App\Services\PageGenerationService::class);
                $aiResult = $aiService->generateClassicPageContent($product, $language);

                if ($aiResult['success'] && isset($aiResult['content'])) {
                    // Store AI-generated content as JSON in the content field
                    $currentContent = $aiResult['content'];
                    $currentContent['language'] = $language;
                    $data['content'] = json_encode($currentContent);
                } else {
                    // If AI generation fails, keep existing content but update language
                    $currentContent['language'] = $language;
                    $data['content'] = json_encode($currentContent);
                    Log::warning('Failed to regenerate AI content for classic page', [
                        'page_id' => $page->id,
                        'product_id' => $product->id,
                        'error' => $aiResult['message'] ?? 'Unknown error'
                    ]);
                }
            } else {
                // No product, just update language in content
                $currentContent['language'] = $language;
                $data['content'] = json_encode($currentContent);
            }

            // Prepare page data
            $data = $this->preparePageData(array_merge($validated, $data), $user);

            // Update slug if title changed
            if (isset($validated['title']) && $validated['title'] !== $page->title) {
                $englishTitle = $this->convertToEnglish($validated['title']);
                $baseSlug = Str::slug($englishTitle);

                // If slug is empty, use default
                if (empty($baseSlug)) {
                    $baseSlug = 'page';
                }

                $data['slug'] = $baseSlug . '-' . $page->id;
            }

            $data['settings'] = $currentSettings;
            $data = $this->applyPackageLimits($data, $user);

            // Update the page
            $page->update($data);

            return redirect()->route('user.pages.index')
                ->with('success', $this->getTranslatedMessage('messages.page_updated', 'Page updated successfully'));
        }

        // AI update - similar to store but update existing page
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'language' => 'required|in:ar,en',
            'api_key' => 'nullable|string',
        ]);

        // Verify product ownership
        $product = Product::findOrFail($validated['product_id']);
        $this->validateProductOwnership($product->id, $user);

        // Get AI service
        $aiService = app(\App\Services\AIService::class);
        $apiKey = $validated['api_key'] ?? null;
        if (!$apiKey && !$aiService->isAvailable()) {
            return redirect()->back()
                ->withInput()
                ->with('error', $this->getTranslatedMessage('messages.ai_not_configured', 'AI service is not configured. Please contact administrator.'));
        }

        // Prepare page data for AI generation
        $shippingPrice = $product->shipping_price_cents ? ($product->shipping_price_cents / 100) : 15.0;

        // Convert form_fields from JSON string to array if needed
        $formFields = $request->input('form_fields', []);
        if (is_string($formFields)) {
            $formFields = json_decode($formFields, true) ?? [];
        }
        if (!is_array($formFields)) {
            $formFields = [];
        }

        $pageData = [
            'title' => $product->name,
            'additional_description' => $product->description ?? '',
            'seo_title' => $product->name,
            'seo_description' => $product->short_description ?? $product->description ?? '',
            'seo_keywords' => '',
            'form_type' => $request->input('form_type', 'default'),
            'form_fields' => $formFields,
            'shipping_price' => $shippingPrice,
            'template_id' => null,
        ];

        // Update page status to pending for regeneration
        $page->update([
            'generation_status' => 'pending',
            'generation_error' => null,
        ]);

        // Dispatch job to regenerate page in background
        \App\Jobs\GeneratePageJob::dispatch($page, $product, $pageData, $validated['language'], $apiKey);

        return redirect()->route('user.pages.index')
            ->with('success', $this->getTranslatedMessage('messages.page_regeneration_started', 'Page regeneration started. It will be ready shortly.'));
    }

    /**
     * Test Gemini API key
     */
    public function testApiKey(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
        ]);

        $aiService = app(\App\Services\AIService::class);
        $result = $aiService->testGeminiApiKey($request->input('api_key'));

        return response()->json($result);
    }

    /**
     * Publish a page
     */
    public function publish(Request $request, Page $page)
    {
        $user = $request->user();

        // Verify ownership
        $this->verifyPageOwnership($page, $user);

        // Check if page generation is completed
        if ($page->generation_status !== 'completed') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $this->getTranslatedMessage('messages.page_generation_not_completed', 'Page generation is not completed yet. Please wait.'),
                ], 400);
            }

            return redirect()->back()
                ->with('error', $this->getTranslatedMessage('messages.page_generation_not_completed', 'Page generation is not completed yet. Please wait.'));
        }

        // Update status to published
        $page->update(['status' => 'published']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $this->getTranslatedMessage('messages.page_published', 'Page published successfully'),
            ]);
        }

        return redirect()->back()
            ->with('success', $this->getTranslatedMessage('messages.page_published', 'Page published successfully'));
    }

    /**
     * Check page generation status
     */
    public function checkStatus(Request $request, Page $page)
    {
        $user = $request->user();

        // Verify ownership
        $this->verifyPageOwnership($page, $user);

        return response()->json([
            'status' => $page->generation_status,
            'error' => $page->generation_error,
            'completed' => $page->generation_status === 'completed',
            'failed' => $page->generation_status === 'failed',
        ]);
    }

    /**
     * Update additional sales settings
     */
    public function updateAdditionalSales(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'additional_sales_enabled' => 'required|boolean',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => TranslationHelper::get('messages.additional_sales_updated'),
        ]);
    }

    /**
     * Process variations (sizes/colors) - filter out empty entries
     */
    private function processVariations(array $variations, bool $isColor = false): array
    {
        $processed = [];
        foreach ($variations as $variation) {
            if (!empty($variation['name'])) {
                $item = [
                    'name' => trim($variation['name']),
                ];

                if ($isColor) {
                    // For colors: store value (can be hex color code or image URL)
                    if (!empty($variation['value'])) {
                        $item['value'] = trim($variation['value']);
                    } elseif (!empty($variation['image'])) {
                        // Backward compatibility: convert 'image' to 'value'
                        $item['value'] = trim($variation['image']);
                    }
                }
                // For sizes: no additional fields needed

                $processed[] = $item;
            }
        }
        return $processed;
    }

    /**
     * Remove unsupported features from data
     */
    private function removeUnsupportedFeatures(array $data, $package = null): array
    {
        if (!$package) {
            // If no package, remove all paid features
            return array_merge($data, [
                'facebook_pixel' => null,
                'tiktok_pixel' => null,
                'snapchat_pixel' => null,
                'google_analytics_id' => null,
            ]);
        }

        // Remove features not supported by package
        if (!$package->hasFeature('facebook_pixel')) {
            $data['facebook_pixel'] = null;
        }
        if (!$package->hasFeature('tiktok_pixel')) {
            $data['tiktok_pixel'] = null;
        }
        if (!$package->hasFeature('snapchat_pixel')) {
            $data['snapchat_pixel'] = null;
        }
        if (!$package->hasFeature('google_analytics')) {
            $data['google_analytics_id'] = null;
        }

        return $data;
    }

    /**
     * Apply package limits to page data
     */
    private function applyPackageLimits(array $data, $user): array
    {
        $subscriptionData = $this->getSubscriptionData($user);
        $package = $subscriptionData['package'] ?? null;

        return $this->removeUnsupportedFeatures($data, $package);
    }

    /**
     * Prepare page data with defaults
     */
    private function preparePageData(array $data, $user): array
    {
        $defaults = [
            'status' => 'draft',
            'form_type' => 'default',
            'form_fields' => [],
            'ai_version' => 'v2',
        ];

        return array_merge($defaults, $data);
    }

    /**
     * Validate page data
     */
    private function validatePageData(Request $request): array
    {
        // Convert form_fields from JSON string to array before validation
        $requestData = $request->all();
        if (isset($requestData['form_fields']) && is_string($requestData['form_fields'])) {
            $decoded = json_decode($requestData['form_fields'], true);
            $requestData['form_fields'] = is_array($decoded) ? $decoded : [];
            $request->merge($requestData);
        }

        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
            ],
            'language' => 'required|in:ar,en',
            'product_id' => 'nullable|exists:products,id',
            'template_id' => 'nullable|exists:landing_page_templates,id',
            'content' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:255',
            'additional_description' => 'nullable|string',
            'form_type' => 'required|in:default,custom',
            'form_fields' => 'nullable|array',
        ], [
            'title.regex' => $this->getTranslatedMessage('messages.title_english_only', 'Page title must contain only English letters, numbers, spaces, hyphens and underscores'),
        ]);
    }

    /**
     * Process form fields
     */
    private function processFormFields(array $data): array
    {
        if ($data['form_type'] === 'custom' && isset($data['form_fields'])) {
            // Ensure form_fields is an array
            if (is_string($data['form_fields'])) {
                $data['form_fields'] = json_decode($data['form_fields'], true) ?? [];
            }
        } else {
            $data['form_fields'] = [];
        }

        return $data;
    }

    /**
     * Verify page ownership
     */
    protected function verifyPageOwnership(Page $page, $user): void
    {
        if ($page->user_id !== $user->id) {
            abort(403);
        }
    }

    /**
     * Verify product ownership
     */
    private function validateProductOwnership(int $productId, $user): void
    {
        $product = Product::where('id', $productId)
            ->where('user_id', $user->id)
            ->firstOrFail();
    }

    /**
     * Delete a page and its AI-generated template file if exists
     */
    public function destroy(Request $request, Page $page)
    {
        $user = $request->user();

        // Verify ownership
        $this->verifyPageOwnership($page, $user);

        try {
            // Check if page has an AI-generated template (category === 'ai-generated')
            // Only delete template if it's AI-generated, not classic templates
            if ($page->template_id) {
                $template = $page->template;

                if ($template && $template->category === 'ai-generated') {
                    // Check if this is the only page using this template
                    $pagesUsingTemplate = Page::where('template_id', $template->id)->count();

                    // Delete template file if exists
                    if ($template->template_file) {
                        $templatePath = resource_path('views/landing-templates/' . $template->template_file);
                        if (file_exists($templatePath)) {
                            unlink($templatePath);
                        }
                    }

                    // Delete template from database if no other pages are using it
                    if ($pagesUsingTemplate <= 1) {
                        $template->delete();
                    }
                }
                // If template is NOT ai-generated (classic template), do NOT delete it
            }

            // Delete the page
            $page->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $this->getTranslatedMessage('messages.page_deleted', 'Page deleted successfully'),
                ]);
            }

            return redirect()->route('user.pages.index')
                ->with('success', $this->getTranslatedMessage('messages.page_deleted', 'Page deleted successfully'));
        } catch (\Exception $e) {
            Log::error('Error deleting page: ' . $e->getMessage(), [
                'page_id' => $page->id,
                'exception' => $e,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $this->getTranslatedMessage('messages.page_delete_error', 'Error deleting page'),
                ], 500);
            }

            return redirect()->back()
                ->with('error', $this->getTranslatedMessage('messages.page_delete_error', 'Error deleting page'));
        }
    }

    /**
     * Update tracking IDs for a page
     */
    public function updateTrackingIds(Request $request, Page $page)
    {
        $user = $request->user();

        // Verify ownership
        $this->verifyPageOwnership($page, $user);

        // Get subscription data and package features
        $subscriptionData = $this->getSubscriptionData($user);
        $currentPackage = $subscriptionData['package'] ?? null;

        // Validate input
        $validated = $request->validate([
            'facebook_pixel' => 'nullable|string|max:255',
            'tiktok_pixel' => 'nullable|string|max:255',
            'snapchat_pixel' => 'nullable|string|max:255',
            'google_analytics_id' => 'nullable|string|max:255',
        ]);

        // Prepare update data
        $updateData = [];

        // Only update features that are available in the user's package
        if ($currentPackage && $currentPackage->hasFeature('facebook_pixel')) {
            $updateData['facebook_pixel'] = $validated['facebook_pixel'] ?? null;
        } else {
            $updateData['facebook_pixel'] = null;
        }

        if ($currentPackage && $currentPackage->hasFeature('tiktok_pixel')) {
            $updateData['tiktok_pixel'] = $validated['tiktok_pixel'] ?? null;
        } else {
            $updateData['tiktok_pixel'] = null;
        }

        if ($currentPackage && $currentPackage->hasFeature('snapchat_pixel')) {
            $updateData['snapchat_pixel'] = $validated['snapchat_pixel'] ?? null;
        } else {
            $updateData['snapchat_pixel'] = null;
        }

        if ($currentPackage && $currentPackage->hasFeature('google_analytics')) {
            $updateData['google_analytics_id'] = $validated['google_analytics_id'] ?? null;
        } else {
            $updateData['google_analytics_id'] = null;
        }

        // Update the page
        $page->update($updateData);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $this->getTranslatedMessage('messages.tracking_ids_updated', 'Tracking IDs updated successfully'),
            ]);
        }

        return redirect()->back()
            ->with('success', $this->getTranslatedMessage('messages.tracking_ids_updated', 'Tracking IDs updated successfully'));
    }

    /**
     * Update page settings
     */
    public function updateSettings(Request $request, Page $page)
    {
        $user = $request->user();

        // Verify ownership
        $this->verifyPageOwnership($page, $user);

        // Validate input
        $validated = $request->validate([
            'smart_assistant_enabled' => 'nullable|boolean',
            'fixed_order_button_enabled' => 'nullable|boolean',
            'fixed_order_button_text' => 'nullable|string|max:255',
            'fixed_order_button_position' => 'nullable|string|in:top-left,top-right,bottom-left,bottom-right',
            'fixed_order_button_color' => 'nullable|string|max:7',
            'fixed_order_button_shape' => 'nullable|string|in:rounded,square,circle',
            'fixed_order_button_size' => 'nullable|string|in:small,medium,large',
            'fixed_order_button_icon' => 'nullable|string|max:50',
            'smart_coupons_enabled' => 'nullable|boolean',
            'remaining_items_counter_enabled' => 'nullable|boolean',
        ]);

        // Get current settings or initialize empty array
        $settings = $page->settings ?? [];

        // Update settings
        $settings = array_merge($settings, [
            'smart_assistant_enabled' => $validated['smart_assistant_enabled'] ?? false,
            'fixed_order_button_enabled' => $validated['fixed_order_button_enabled'] ?? false,
            'fixed_order_button_text' => $validated['fixed_order_button_text'] ?? 'اطلب الآن',
            'fixed_order_button_position' => $validated['fixed_order_button_position'] ?? 'bottom-right',
            'fixed_order_button_color' => $validated['fixed_order_button_color'] ?? '#667eea',
            'fixed_order_button_shape' => $validated['fixed_order_button_shape'] ?? 'rounded',
            'fixed_order_button_size' => $validated['fixed_order_button_size'] ?? 'medium',
            'fixed_order_button_icon' => $validated['fixed_order_button_icon'] ?? 'cart-shopping',
            'smart_coupons_enabled' => $validated['smart_coupons_enabled'] ?? false,
            'remaining_items_counter_enabled' => $validated['remaining_items_counter_enabled'] ?? false,
        ]);

        // Update the page
        $page->update(['settings' => $settings]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $this->getTranslatedMessage('messages.page_settings_updated', 'Page settings updated successfully'),
            ]);
        }

        return redirect()->back()
            ->with('success', $this->getTranslatedMessage('messages.page_settings_updated', 'Page settings updated successfully'));
    }

    /**
     * Create a link for a page
     */
    public function createLink(Request $request, Page $page)
    {
        $user = $request->user();

        // Verify ownership
        $this->verifyPageOwnership($page, $user);

        // Validate input
        $validated = $request->validate([
            'custom_domain' => 'nullable|string|max:255',
            'store_name' => 'nullable|string|max:255',
        ]);

        // Update the page
        $page->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $this->getTranslatedMessage('messages.page_link_created', 'Page link created successfully'),
                'page_url' => route('page.show', $page->id),
            ]);
        }

        return redirect()->back()
            ->with('success', $this->getTranslatedMessage('messages.page_link_created', 'Page link created successfully'));
    }

    /**
     * Show a page (for JSON response)
     */
    public function show(Request $request, Page $page)
    {
        $user = $request->user();

        // Verify ownership
        $this->verifyPageOwnership($page, $user);

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'status' => $page->status,
                'facebook_pixel' => $page->facebook_pixel,
                'tiktok_pixel' => $page->tiktok_pixel,
                'snapchat_pixel' => $page->snapchat_pixel,
                'google_analytics_id' => $page->google_analytics_id,
                'settings' => $page->settings,
            ]);
        }

        return redirect()->route('user.pages.index');
    }

    /**
     * Get variations (sizes and colors) for a page
     */
    public function getVariations(Page $page)
    {
        $user = request()->user();
        $this->verifyPageOwnership($page, $user);

        $settings = $page->settings ?? [];

        return response()->json([
            'sizes' => $settings['sizes'] ?? [],
            'colors' => $settings['colors'] ?? [],
        ]);
    }

    /**
     * Update variations (sizes and colors) for a page
     */
    public function updateVariations(Request $request, Page $page)
    {
        $user = $request->user();
        $this->verifyPageOwnership($page, $user);

        $validated = $request->validate([
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'required_with:sizes|string|max:255',
            'colors' => 'nullable|array',
            'colors.*.name' => 'required_with:colors|string|max:255',
            'colors.*.value' => 'nullable|string|max:500', // Can be hex color code or image URL
        ]);

        // Process variations
        $sizes = $this->processVariations($validated['sizes'] ?? [], false);
        $colors = $this->processVariations($validated['colors'] ?? [], true);

        // Get current settings and merge with sizes and colors
        $currentSettings = $page->settings ?? [];
        $currentSettings['sizes'] = $sizes;
        $currentSettings['colors'] = $colors;

        // Update the page
        $page->update(['settings' => $currentSettings]);

        // Always return JSON (the request is always AJAX from the modal)
        return response()->json([
            'success' => true,
            'message' => $this->getTranslatedMessage('messages.variations_updated', 'Variations updated successfully'),
        ]);
    }

    /**
     * Convert Arabic/other text to English for slug generation
     * This is a simple transliteration - for production, consider using a library like Laravel Translatable
     */
    protected function convertToEnglish(string $text): string
    {
        // Remove Arabic and other non-ASCII characters, keep only English letters, numbers, and spaces
        $text = preg_replace('/[^\x00-\x7F]+/u', '', $text);

        // If text becomes empty, use a default
        if (empty(trim($text))) {
            $text = 'page';
        }

        return trim($text);
    }

    /**
     * Update slug after page creation to include ID
     */
    protected function updatePageSlug(Page $page): void
    {
        // Get base slug from title (not from current slug which may have temp ID)
        $englishTitle = $this->convertToEnglish($page->title ?? 'page');
        $baseSlug = Str::slug($englishTitle);

        // If slug is empty, use default
        if (empty($baseSlug)) {
            $baseSlug = 'page';
        }

        // Update with final slug: base-slug-id (no random numbers)
        $page->update([
            'slug' => $baseSlug . '-' . $page->id
        ]);
    }

    /**
     * Generate slug for a page that doesn't have one
     */
    protected function generateSlugForPage(Page $page): void
    {
        $englishTitle = $this->convertToEnglish($page->title ?? 'page');
        $baseSlug = Str::slug($englishTitle);

        // If slug is empty, use default
        if (empty($baseSlug)) {
            $baseSlug = 'page';
        }

        // Update with final slug: base-slug-id
        $page->update([
            'slug' => $baseSlug . '-' . $page->id
        ]);

        // Refresh the model to get the updated slug
        $page->refresh();
    }
}
