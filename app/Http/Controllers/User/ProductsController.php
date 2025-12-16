<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Concerns\EnforcesPackageLimits;
use App\Http\Controllers\Concerns\HasLocaleAndTranslation;
use App\Http\Controllers\Concerns\HasSubscriptionHelper;
use App\Http\Controllers\Controller;
use App\Models\AdditionalSetting;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Helpers\TranslationHelper;

class ProductsController extends Controller
{
    use EnforcesPackageLimits, HasLocaleAndTranslation, HasSubscriptionHelper;

    public function index(Request $request)
    {
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        $query = Product::where('user_id', $request->user()->id)
            ->with('pages');

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('short_description', 'like', '%' . $search . '%');
            });
        }

        // Currency filter
        if ($request->has('currency') && $request->currency) {
            $query->where('currency', $request->currency);
        }

        // Price range filter
        if ($request->has('price_min') && $request->price_min) {
            $query->where('price_cents', '>=', $request->price_min * 100);
        }
        if ($request->has('price_max') && $request->price_max) {
            $query->where('price_cents', '<=', $request->price_max * 100);
        }

        // Date range filter
        if ($request->has('created_from') && $request->created_from) {
            $query->whereDate('created_at', '>=', $request->created_from);
        }
        if ($request->has('created_to') && $request->created_to) {
            $query->whereDate('created_at', '<=', $request->created_to);
        }

        $products = $query->latest()->get();

        // Process product images for each product
        $products->each(function ($product) {
            $images = is_array($product->images) ? $product->images : (is_string($product->images) ? json_decode($product->images, true) : []);
            $product->processed_images = is_array($images) ? $images : [];
        });

        return view('user.products.index', compact('products', 'locale', 'dir', 't'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        // Get default currency from settings
        $settingsService = app(\App\Services\SettingsService::class);
        $defaultCurrency = $settingsService->getCurrencyCode();

        // Prepare currencies list
        $currencies = [
            'EGP' => $t('messages.currency_egp') ?? 'Egyptian Pound',
            'USD' => $t('messages.currency_usd') ?? 'US Dollar',
            'EUR' => $t('messages.currency_eur') ?? 'Euro',
            'GBP' => $t('messages.currency_gbp') ?? 'British Pound',
            'SAR' => $t('messages.currency_sar') ?? 'Saudi Riyal',
            'AED' => $t('messages.currency_aed') ?? 'UAE Dirham',
            'KWD' => $t('messages.currency_kwd') ?? 'Kuwaiti Dinar',
            'QAR' => $t('messages.currency_qar') ?? 'Qatari Riyal',
            'BHD' => $t('messages.currency_bhd') ?? 'Bahraini Dinar',
            'OMR' => $t('messages.currency_omr') ?? 'Omani Rial',
        ];

        // Get AI providers
        $additionalSettings = AdditionalSetting::getForUser($user->id);
        $availableProviders = $this->getAvailableProviders($additionalSettings);
        $defaultProvider = $additionalSettings->ai_provider ?? 'gemini';

        // Check if user is on free plan
        $isFreePlan = $this->isUserOnFreePlan($user);

        // Check if editing existing product
        $product = null;
        if ($request->has('edit')) {
            $productId = $request->input('edit');
            $product = Product::where('id', $productId)
                ->where('user_id', $user->id)
                ->first();

            if (!$product) {
                return redirect()->route('user.products.index')
                    ->with('error', $this->getTranslatedMessage('messages.product_not_found', 'Product not found'));
            }
        }

        return view('user.products.create', compact(
            'locale',
            'dir',
            't',
            'currencies',
            'defaultCurrency',
            'availableProviders',
            'defaultProvider',
            'isFreePlan',
            'product'
        ));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Check limits
        $count = Product::where('user_id', $user->id)->count();

        // Check limit and return redirect with flash message if limit reached
        $limitCheck = $this->checkLimitWithRedirect($user, 'products_limit', $count, route('user.products.index'));
        if ($limitCheck) {
            return $limitCheck->withInput();
        }

        // Validate input data
        try {
            $data = $this->validateProductData($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // Prepare product data
        $data = $this->prepareProductData($data, $user, $request);

        // Create product
        try {
            Product::create($data);
            return redirect()->route('user.products.index')
                ->with('success', $this->getTranslatedMessage('messages.product_created', 'Product created successfully'));
        } catch (\Exception $e) {
            Log::error('Product creation failed', ['error' => $e->getMessage(), 'data' => $data]);
            return redirect()->back()->withInput()->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function edit(Product $product, Request $request)
    {
        $user = $request->user();

        // Verify product belongs to user
        if ($product->user_id !== $user->id) {
            return redirect()->route('user.products.index')
                ->with('error', $this->getTranslatedMessage('messages.unauthorized', 'Unauthorized'));
        }

        ['locale' => $locale, 'dir' => $dir, 't' => $t] = $this->getLocaleData();

        // Get default currency from settings
        $settingsService = app(\App\Services\SettingsService::class);
        $defaultCurrency = $settingsService->getCurrencyCode();

        // Prepare currencies list
        $currencies = [
            'EGP' => $t('messages.currency_egp') ?? 'Egyptian Pound',
            'USD' => $t('messages.currency_usd') ?? 'US Dollar',
            'EUR' => $t('messages.currency_eur') ?? 'Euro',
            'GBP' => $t('messages.currency_gbp') ?? 'British Pound',
            'SAR' => $t('messages.currency_sar') ?? 'Saudi Riyal',
            'AED' => $t('messages.currency_aed') ?? 'UAE Dirham',
            'KWD' => $t('messages.currency_kwd') ?? 'Kuwaiti Dinar',
            'QAR' => $t('messages.currency_qar') ?? 'Qatari Riyal',
            'BHD' => $t('messages.currency_bhd') ?? 'Bahraini Dinar',
            'OMR' => $t('messages.currency_omr') ?? 'Omani Rial',
        ];

        // Get AI providers
        $additionalSettings = AdditionalSetting::getForUser($user->id);
        $availableProviders = $this->getAvailableProviders($additionalSettings);
        $defaultProvider = $additionalSettings->ai_provider ?? 'gemini';

        // Check if user is on free plan
        $isFreePlan = $this->isUserOnFreePlan($user);

        return view('user.products.create', compact(
            'locale',
            'dir',
            't',
            'currencies',
            'defaultCurrency',
            'availableProviders',
            'defaultProvider',
            'isFreePlan',
            'product'
        ));
    }

    public function update(Request $request, Product $product)
    {
        $user = $request->user();

        // Verify product belongs to user
        if ($product->user_id !== $user->id) {
            return redirect()->route('user.products.index')
                ->with('error', $this->getTranslatedMessage('messages.unauthorized', 'Unauthorized'));
        }

        // Validate input data
        try {
            $data = $this->validateProductData($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // Prepare product data - preserve old values if not provided
        $data = $this->prepareProductDataForUpdate($data, $product, $user, $request);

        // Update product
        try {
            $product->update($data);
            return redirect()->route('user.products.index')
                ->with('success', $this->getTranslatedMessage('messages.product_updated', 'Product updated successfully'));
        } catch (\Exception $e) {
            Log::error('Product update failed', ['error' => $e->getMessage(), 'data' => $data]);
            return redirect()->back()->withInput()->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Validate product data
     */
    protected function validateProductData(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:250',
            'description' => 'nullable|string',
            'price_cents' => 'nullable|numeric|min:0',
            'shipping_price_cents' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'ai_version' => 'nullable|in:gemini,openai',
            'images' => 'nullable|string',
            'image_files' => 'nullable|array',
            'image_files.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    }

    /**
     * Prepare product data for saving
     */
    protected function prepareProductData(array $data, $user, Request $request): array
    {
        $data['user_id'] = $user->id;
        $data['price_cents'] = $this->convertToCents($data['price_cents'] ?? 0);
        $data['shipping_price_cents'] = $this->convertToCents($data['shipping_price_cents'] ?? 0);
        $data['currency'] = $data['currency'] ?? $this->getDefaultCurrency();
        $data['ai_version'] = $data['ai_version'] ?? $this->getDefaultAiVersion($user);
        $data['images'] = $this->processProductImages($request, $data['images'] ?? null);

        unset($data['image_files']);

        return $data;
    }

    /**
     * Prepare product data for update - preserve old values if not provided
     */
    protected function prepareProductDataForUpdate(array $data, Product $product, $user, Request $request): array
    {
        $data['user_id'] = $user->id;

        // Preserve old name if not provided
        if (empty($data['name'])) {
            $data['name'] = $product->name;
        }

        // Preserve old short_description if not provided
        if (!isset($data['short_description']) || $data['short_description'] === '') {
            $data['short_description'] = $product->short_description;
        }

        // Preserve old description if not provided
        if (!isset($data['description']) || $data['description'] === '') {
            $data['description'] = $product->description;
        }

        // Preserve old price if not provided or is 0
        if (isset($data['price_cents']) && $data['price_cents'] > 0) {
            $data['price_cents'] = $this->convertToCents($data['price_cents']);
        } else {
            $data['price_cents'] = $product->price_cents;
        }

        // Preserve old shipping price if not provided
        if (isset($data['shipping_price_cents']) && $data['shipping_price_cents'] !== null && $data['shipping_price_cents'] !== '') {
            $data['shipping_price_cents'] = $this->convertToCents($data['shipping_price_cents']);
        } else {
            $data['shipping_price_cents'] = $product->shipping_price_cents ?? 0;
        }

        // Preserve old currency if not provided
        if (empty($data['currency'])) {
            $data['currency'] = $product->currency ?? $this->getDefaultCurrency();
        }

        // Preserve old AI version if not provided
        if (empty($data['ai_version'])) {
            $data['ai_version'] = $product->ai_version ?? $this->getDefaultAiVersion($user);
        }

        // Process images - preserve existing images if no new ones are provided
        $existingImages = $product->images ?? [];

        // Check if new images were uploaded or provided via input
        $hasNewImages = $request->hasFile('image_files') || !empty($data['images']);

        if ($hasNewImages) {
            // Process new images
            $newImages = $this->processProductImages($request, $data['images'] ?? null);

            // If new images were successfully processed, use them
            if ($newImages && !empty($newImages)) {
                $data['images'] = $newImages;
            } else {
                // If processing failed, keep existing images
                $data['images'] = $existingImages;
            }
        } else {
            // No new images provided, keep existing ones
            $data['images'] = $existingImages;
        }

        unset($data['image_files']);

        return $data;
    }

    /**
     * Convert decimal price to cents
     */
    protected function convertToCents($value): int
    {
        if (!is_numeric($value)) {
            return 0;
        }
        return (int) round($value * 100);
    }

    /**
     * Get default currency
     */
    protected function getDefaultCurrency(): string
    {
        try {
            $settingsService = app(\App\Services\SettingsService::class);
            return $settingsService->getCurrencyCode();
        } catch (\Exception $e) {
            Log::error('SettingsService error', ['error' => $e->getMessage()]);
            return 'EGP'; // Fallback
        }
    }

    /**
     * Get default AI version
     */
    protected function getDefaultAiVersion($user): string
    {
        try {
            $additionalSettings = AdditionalSetting::getForUser($user->id);
            return $additionalSettings->ai_provider ?? 'gemini';
        } catch (\Exception $e) {
            Log::error('AdditionalSetting error', ['error' => $e->getMessage()]);
            return 'gemini'; // Fallback
        }
    }

    /**
     * Process product images from files and URLs
     */
    protected function processProductImages(Request $request, ?string $imagesInput): ?array
    {
        $imageUrls = [];

        // Process uploaded files
        if ($request->hasFile('image_files')) {
            try {
                foreach ($request->file('image_files') as $file) {
                    $imageName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $imagePath = $file->storeAs('products', $imageName, 'public');
                    $imageUrls[] = Storage::url($imagePath);
                }
            } catch (\Exception $e) {
                Log::error('Image upload failed', ['error' => $e->getMessage()]);
            }
        }

        // Process image URLs from input
        if (!empty($imagesInput) && is_string($imagesInput)) {
            try {
                $urlsFromInput = json_decode($imagesInput, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($urlsFromInput)) {
                    $imageUrls = array_merge($imageUrls, $urlsFromInput);
                } else {
                    $urls = array_filter(array_map('trim', explode(',', $imagesInput)));
                    $imageUrls = array_merge($imageUrls, $urls);
                }
            } catch (\Exception $e) {
                Log::error('Image URL processing failed', ['error' => $e->getMessage()]);
            }
        }

        $imageUrls = array_values(array_unique(array_filter($imageUrls)));
        return !empty($imageUrls) ? $imageUrls : null;
    }

    /**
     * Get available AI providers
     */
    private function getAvailableProviders(AdditionalSetting $settings): array
    {
        $providers = [];
        $defaultProvider = $settings->ai_provider ?? 'gemini';

        // Gemini (always free)
        $geminiKey = !empty($settings->ai_gemini_api_key);
        $providers['gemini'] = [
            'name' => 'Google Gemini',
            'is_free' => true,
            'has_api_key' => $geminiKey,
        ];

        // OpenAI (always paid)
        $openaiKey = !empty($settings->ai_openai_api_key);
        $providers['openai'] = [
            'name' => 'OpenAI ChatGPT',
            'is_free' => false,
            'has_api_key' => $openaiKey,
        ];

        return $providers;
    }

    /**
     * Get translated message with fallback
     */
    protected function getTranslatedMessage(string $messageKey, string $fallback): string
    {
        return TranslationHelper::get($messageKey) ?? $fallback;
    }
}
