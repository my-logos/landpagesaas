<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Order;
use App\Models\Product;
use App\Services\FacebookConversionAPIService;
use App\Models\FacebookConversionAPISetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;

class PageViewController extends Controller
{
    /**
     * Display a landing page to visitors
     */
    public function show($id)
    {
        try {
            $page = Page::with(['product', 'template', 'user'])->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, app()->getLocale() === 'ar' ? 'الصفحة غير موجودة' : 'Page not found');
        }

        // Only show published pages, but allow preview for page owner
        if ($page->status !== 'published') {
            // Allow preview if user is authenticated and owns the page
            $user = auth()->user();
            if ($user && $page->user_id === $user->id) {
                // Allow preview for page owner
            } elseif (app()->environment('local') && $page->status === 'draft') {
                // Allow draft pages in local environment for testing
            } else {
                abort(404, app()->getLocale() === 'ar' ? 'الصفحة غير منشورة' : 'Page is not published');
            }
        }

        // Prepare all data for template
        $data = $this->prepareTemplateData($page);

        $templateView = $this->getTemplateView($page);
        if ($templateView) {
            return view($templateView, $data);
        }

        // Fallback to default landing page view
        return view('landing-templates.default', $data);
    }

    /**
     * Store order from landing page
     */
    public function storeOrder(Request $request, $id)
    {
        \Log::info('Order submission started', ['page_id' => $id, 'ip' => $request->ip()]);

        try {
            $page = Page::with(['product', 'user'])->findOrFail($id);
            \Log::info('Page found', ['page_id' => $id]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Page not found for order', ['page_id' => $id, 'slug' => $slug, 'ip' => $request->ip()]);
            abort(404, app()->getLocale() === 'ar' ? 'الصفحة غير موجودة' : 'Page not found');
        }

        // Only allow orders from published pages
        if ($page->status !== 'published') {
            \Log::warning('Order attempt on unpublished page', ['page_id' => $id, 'status' => $page->status, 'ip' => $request->ip()]);
            return redirect()->back()
                ->withErrors(['error' => app()->getLocale() === 'ar' ? 'الصفحة غير منشورة' : 'Page is not published'])
                ->withInput();
        }

        \Log::info('Page is published, proceeding with order');

        // Rate limiting: Check for duplicate orders from same IP/phone in last 5 minutes
        $ipAddress = $request->ip();
        $sessionId = $request->session()->getId();

        // Get page owner's performance settings
        $pageOwner = $page->user;
        $includeSessionData = $pageOwner->include_session_data ?? false;
        $includeLocation = $pageOwner->include_location_data ?? false;
        $includeDeviceType = $pageOwner->include_device_type ?? false;

        \Log::info('Checking for duplicate orders', ['page_id' => $page->id, 'ip' => $ipAddress]);

        // Optimized duplicate check: Skip if no recent orders to speed up
        $recentOrders = 0;
        try {
            // First, quick count check - if no recent orders, skip detailed check
            $recentOrdersCount = Order::where('landing_page_id', $page->id)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->whereNotNull('customer_data')
                ->count();

            if ($recentOrdersCount > 0 && $recentOrdersCount <= 50) {
                // Only do detailed check if there are recent orders
                $recentOrdersQuery = Order::where('landing_page_id', $page->id)
                    ->where('created_at', '>=', now()->subMinutes(5))
                    ->whereNotNull('customer_data')
                    ->limit(50)
                    ->pluck('customer_data');

                // Filter in PHP (faster than multiple JSON_EXTRACT queries)
                $recentOrders = $recentOrdersQuery->filter(function ($customerData) use ($ipAddress, $request, $sessionId, $includeSessionData) {
                    if (!is_array($customerData)) {
                        return false;
                    }

                    // Check by IP address (most common case, check first)
                    if (isset($customerData['ip']) && $customerData['ip'] === $ipAddress) {
                        return true;
                    }

                    // Check by phone if provided
                    if ($request->has('customer_phone') || ($request->has('form_data') && isset($request->form_data['phone']))) {
                        $phone = $request->input('customer_phone') ?? $request->input('form_data.phone');
                        $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);
                        if (isset($customerData['phone'])) {
                            $storedPhone = preg_replace('/[^0-9+]/', '', $customerData['phone']);
                            if ($storedPhone === $cleanPhone || $storedPhone === $phone || $customerData['phone'] === $phone) {
                                return true;
                            }
                        }
                    }

                    // Check by session ID if enabled
                    if ($includeSessionData && $sessionId && isset($customerData['session_id']) && $customerData['session_id'] === $sessionId) {
                        return true;
                    }

                    return false;
                })->count();
            }

            \Log::info('Duplicate check completed', ['recent_orders' => $recentOrders]);
        } catch (\Exception $e) {
            \Log::warning('Error checking duplicate orders, allowing order to proceed', [
                'error' => $e->getMessage(),
                'page_id' => $page->id,
                'trace' => $e->getTraceAsString()
            ]);
            // If there's an error, allow the order to proceed
            $recentOrders = 0;
        }

        if ($recentOrders > 0) {
            return redirect()->back()
                ->withErrors(['error' => app()->getLocale() === 'ar' ? 'تم إرسال طلبك بالفعل. يرجى الانتظار قليلاً.' : 'Your order has already been submitted. Please wait a moment.'])
                ->withInput();
        }

        // Check if page has sizes or colors
        $settings = $page->settings ?? [];
        $hasSizes = !empty($settings['sizes']);
        $hasColors = !empty($settings['colors']);

        // Validate based on form type
        if ($page->form_type === 'default') {
            $validationRules = [
                'product_id' => 'required|exists:products,id',
                'customer_name' => 'required|string|max:255|min:2',
                'customer_phone' => [
                    'required',
                    'string',
                    'max:50',
                    'regex:/^[0-9+\-\s()]+$/', // Only numbers, +, -, spaces, parentheses
                ],
                'customer_address' => 'required|string|max:1000|min:10',
                'quantity' => 'required|integer|min:1',
            ];

            // Add validation for sizes and colors if they exist
            if ($hasSizes) {
                $validationRules['product_size'] = 'required|string';
            }
            if ($hasColors) {
                $validationRules['product_color'] = 'required|string';
            }

            try {
                $validated = $request->validate($validationRules, [
                    'customer_name.min' => app()->getLocale() === 'ar' ? 'الاسم يجب أن يكون على الأقل حرفين' : 'Name must be at least 2 characters',
                    'customer_phone.regex' => app()->getLocale() === 'ar' ? 'رقم الهاتف غير صحيح' : 'Invalid phone number format',
                    'customer_address.min' => app()->getLocale() === 'ar' ? 'العنوان يجب أن يكون على الأقل 10 أحرف' : 'Address must be at least 10 characters',
                    'quantity.required' => app()->getLocale() === 'ar' ? 'الكمية مطلوبة' : 'Quantity is required',
                    'quantity.min' => app()->getLocale() === 'ar' ? 'الكمية يجب أن تكون على الأقل 1' : 'Quantity must be at least 1',
                    'product_size.required' => app()->getLocale() === 'ar' ? 'يرجى اختيار المقاس' : 'Please select a size',
                    'product_color.required' => app()->getLocale() === 'ar' ? 'يرجى اختيار اللون' : 'Please select a color',
                ]);
            } catch (ValidationException $e) {
                \Log::warning('Validation failed for order', [
                    'page_id' => $page->id,
                    'errors' => $e->errors(),
                    'input' => $request->all()
                ]);
                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput();
            }

            $customerData = [
                'name' => trim($validated['customer_name']),
                'phone' => preg_replace('/[^0-9+]/', '', $validated['customer_phone']), // Clean phone number
                'address' => trim($validated['customer_address']),
                'ip' => $ipAddress,
            ];

            // Add size and color if selected
            if ($hasSizes && isset($validated['product_size'])) {
                $customerData['size'] = $validated['product_size'];
            }
            if ($hasColors && isset($validated['product_color'])) {
                $customerData['color'] = $validated['product_color'];
            }

            // Add session data if enabled
            if ($includeSessionData) {
                $customerData['session_id'] = $sessionId;
            }

            // Add location data if enabled - DISABLED TEMPORARILY to speed up order processing
            // Location can be added later via background job if needed
            // if ($includeLocation) {
            //     try {
            //         $city = $this->getLocationFromIP($ipAddress);
            //         if ($city) {
            //             $customerData['city'] = $city;
            //         }
            //     } catch (\Exception $e) {
            //         \Log::debug('Failed to get location for order', ['ip' => $ipAddress]);
            //     }
            // }

            // Add device type if enabled
            if ($includeDeviceType) {
                $customerData['device_type'] = $this->detectDeviceType($request);
            }

            \Log::info('Customer data prepared for default form', ['has_location' => false]);
        } else {
            // Custom form fields
            $validationRules = [
                'product_id' => 'required|exists:products,id',
                'form_data' => 'required|array',
                'form_data.*' => 'required|string|max:1000',
                'quantity' => 'required|integer|min:1',
            ];

            // Add validation for sizes and colors if they exist
            if ($hasSizes) {
                $validationRules['product_size'] = 'required|string';
            }
            if ($hasColors) {
                $validationRules['product_color'] = 'required|string';
            }

            try {
                $validated = $request->validate($validationRules, [
                    'quantity.required' => app()->getLocale() === 'ar' ? 'الكمية مطلوبة' : 'Quantity is required',
                    'quantity.min' => app()->getLocale() === 'ar' ? 'الكمية يجب أن تكون على الأقل 1' : 'Quantity must be at least 1',
                    'product_size.required' => app()->getLocale() === 'ar' ? 'يرجى اختيار المقاس' : 'Please select a size',
                    'product_color.required' => app()->getLocale() === 'ar' ? 'يرجى اختيار اللون' : 'Please select a color',
                ]);
            } catch (ValidationException $e) {
                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput();
            }

            $customerData = array_map('trim', $validated['form_data']);
            $customerData['ip'] = $ipAddress;

            // Add size and color if selected
            if ($hasSizes && isset($validated['product_size'])) {
                $customerData['size'] = $validated['product_size'];
            }
            if ($hasColors && isset($validated['product_color'])) {
                $customerData['color'] = $validated['product_color'];
            }

            // Add session data if enabled
            if ($includeSessionData) {
                $customerData['session_id'] = $sessionId;
            }

            // Add location data if enabled - DISABLED TEMPORARILY to speed up order processing
            // Location can be added later via background job if needed
            // if ($includeLocation) {
            //     try {
            //         $city = $this->getLocationFromIP($ipAddress);
            //         if ($city) {
            //             $customerData['city'] = $city;
            //         }
            //     } catch (\Exception $e) {
            //         \Log::debug('Failed to get location for order', ['ip' => $ipAddress]);
            //     }
            // }

            // Add device type if enabled
            if ($includeDeviceType) {
                $customerData['device_type'] = $this->detectDeviceType($request);
            }

            \Log::info('Customer data prepared for custom form', ['has_location' => false]);

            // Validate phone if exists
            if (isset($customerData['phone'])) {
                if (!preg_match('/^[0-9+\-\s()]+$/', $customerData['phone'])) {
                    return redirect()->back()
                        ->withErrors(['form_data.phone' => app()->getLocale() === 'ar' ? 'رقم الهاتف غير صحيح' : 'Invalid phone number format'])
                        ->withInput();
                }
                $customerData['phone'] = preg_replace('/[^0-9+]/', '', $customerData['phone']);
            }
        }

        // Verify product belongs to page owner or is linked to page
        $product = Product::findOrFail($validated['product_id']);
        if ($page->product_id && $page->product_id != $product->id) {
            // If page has a specific product, must match
            return redirect()->back()->withErrors(['product_id' => app()->getLocale() === 'ar' ? 'المنتج غير صحيح لهذه الصفحة' : 'Invalid product for this page']);
        }

        // Additional spam protection: Check for suspicious patterns
        $name = $customerData['name'] ?? '';
        $phone = $customerData['phone'] ?? '';

        // Check for repeated characters (spam pattern)
        if (preg_match('/(.)\1{4,}/', $name) || preg_match('/(.)\1{4,}/', $phone)) {
            return redirect()->back()
                ->withErrors(['error' => app()->getLocale() === 'ar' ? 'البيانات المدخلة غير صحيحة' : 'Invalid input data'])
                ->withInput();
        }

        \Log::info('Starting order creation process', ['page_id' => $page->id, 'product_id' => $validated['product_id']]);

        try {
            // Generate unique 4-digit order number
            \Log::info('Generating order number');
            $orderNumber = $this->generateOrderNumber();
            \Log::info('Order number generated', ['order_number' => $orderNumber]);

            // Get quantity from request
            $quantity = $request->input('quantity', 1);
            if (!is_numeric($quantity) || $quantity < 1) {
                $quantity = 1;
            }
            $quantity = (int)$quantity;
            \Log::info('Quantity processed', ['quantity' => $quantity]);

            // Calculate total based on product price + shipping with smart coupons logic
            $settings = $page->settings ?? [];
            $smartCouponsEnabled = $settings['smart_coupons_enabled'] ?? false;
            $originalPrice = $product->price_cents ?? 0;
            $shippingPrice = $product->shipping_price_cents ?? 0;
            $basePrice = $originalPrice + $shippingPrice; // السعر + الشحن (الإجمالي)

            if ($smartCouponsEnabled) {
                // إضافة 25% على الإجمالي (السعر + الشحن)
                $increasedPrice = $basePrice * 1.25;
                // خصم 20% من السعر بعد الزيادة ليعيد السعر للإجمالي الأصلي
                $discountAmount = $increasedPrice * 0.20;
                $finalPrice = $increasedPrice - $discountAmount; // يجب أن يساوي $basePrice
                // السعر النهائي للوحدة الواحدة = الإجمالي الأصلي (السعر + الشحن)
                $unitPrice = $basePrice;
            } else {
                // بدون smart coupons، استخدم سعر المنتج فقط
                $unitPrice = $originalPrice;
            }

            // حساب الإجمالي = السعر للوحدة × الكمية
            $totalCents = $unitPrice * $quantity;
            \Log::info('Price calculated', ['total_cents' => $totalCents, 'unit_price' => $unitPrice]);

            // Create order with page owner's user_id
            \Log::info('Creating order in database');
            $order = Order::create([
                'user_id' => $page->user_id, // Page owner's ID
                'product_id' => $validated['product_id'],
                'landing_page_id' => $page->id,
                'quantity' => $quantity,
                'total_cents' => $totalCents,
                'currency' => $product->currency ?? 'EGP',
                'status' => 'pending',
                'shipping_status' => 'pending',
                'order_number' => $orderNumber,
                'customer_data' => $customerData,
            ]);

            \Log::info('Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $orderNumber,
                'page_id' => $page->id,
                'user_id' => $page->user_id,
                'product_id' => $validated['product_id']
            ]);

            // Send Facebook Conversion API event if enabled
            $this->sendFacebookConversionAPIEvent($page, $order, $product, $customerData, $request);

            // Send webhook event for new order
            $this->sendWebhookEvent('order_received', $order, $page, $product, $customerData);

            // Redirect to thank you page
            \Log::info('Redirecting to thank you page', ['order_id' => $order->id]);
            return redirect()->route('order.thank-you', $order);
        } catch (\Exception $e) {
            \Log::error('Failed to create order', [
                'page_id' => $page->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حفظ الطلب. يرجى المحاولة مرة أخرى.' : 'An error occurred while saving the order. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display thank you page after order submission
     */
    public function thankYou(Order $order)
    {
        $order->load(['product', 'landingPage', 'user']);
        $trackingUrl = route('order.track', ['order_number' => $order->order_number]);

        // Get page owner's additional sales setting
        $pageOwner = $order->landingPage ? $order->landingPage->user : $order->user;
        $additionalSalesEnabled = $pageOwner->additional_sales_enabled ?? false;

        // Get page owner's products if additional sales is enabled
        $additionalProducts = [];
        if ($additionalSalesEnabled && $pageOwner) {
            $additionalProducts = Product::where('user_id', $pageOwner->id)
                ->where('id', '!=', $order->product_id ?? 0)
                ->whereNotNull('images')
                ->limit(6)
                ->get();
        }

        // Process customer data
        $customerData = $order->customer_data ?? [];
        $size = $customerData['size'] ?? null;
        $color = $customerData['color'] ?? null;

        // Process additional products data
        $processedAdditionalProducts = [];
        foreach ($additionalProducts as $product) {
            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'short_description' => $product->short_description,
                'price_cents' => $product->price_cents,
                'currency' => $product->currency ?? 'EGP',
                'first_image' => null,
                'page_id' => null,
            ];

            // Get first image
            $images = $product->images;
            if (is_string($images)) {
                $images = json_decode($images, true);
            }
            if ($images && is_array($images) && count($images) > 0) {
                $productData['first_image'] = $images[0] ?? null;
            }

            // Find page for this product
            $productPage = Page::where('product_id', $product->id)
                ->where('status', 'published')
                ->first();

            if ($productPage) {
                $productData['page_id'] = $productPage->id;
            } elseif ($order->landingPage) {
                $orderPage = Page::find($order->landing_page_id);
                if ($orderPage) {
                    $productData['page_id'] = $orderPage->id;
                }
            }

            $processedAdditionalProducts[] = $productData;
        }

        // Get landing page for back link
        $landingPage = null;
        if ($order->landing_page_id) {
            $landingPage = Page::find($order->landing_page_id);
        }

        $locale = app()->getLocale();
        $dir = $locale === 'ar' ? 'rtl' : 'ltr';

        return view('order.thank-you', compact(
            'order',
            'trackingUrl',
            'additionalProducts',
            'processedAdditionalProducts',
            'additionalSalesEnabled',
            'locale',
            'dir',
            'size',
            'color',
            'landingPage'
        ));
    }

    /**
     * Track order status by order number
     */
    public function trackOrder(Request $request, $order_number)
    {
        $order = Order::where('order_number', $order_number)
            ->with(['product', 'landingPage', 'user'])
            ->firstOrFail();

        // Get page owner's additional sales setting
        $pageOwner = $order->landingPage ? $order->landingPage->user : $order->user;
        $additionalSalesEnabled = $pageOwner->additional_sales_enabled ?? false;

        // Get page owner's products if additional sales is enabled
        $additionalProducts = [];
        if ($additionalSalesEnabled && $pageOwner) {
            $additionalProducts = Product::where('user_id', $pageOwner->id)
                ->where('id', '!=', $order->product_id ?? 0)
                ->whereNotNull('images')
                ->limit(6)
                ->get();
        }

        // Process customer data
        $customerData = $order->customer_data ?? [];
        $size = $customerData['size'] ?? null;
        $color = $customerData['color'] ?? null;

        // Process additional products data
        $processedAdditionalProducts = [];
        foreach ($additionalProducts as $product) {
            $productData = [
                'id' => $product->id,
                'name' => $product->name,
                'short_description' => $product->short_description,
                'price_cents' => $product->price_cents,
                'currency' => $product->currency ?? 'EGP',
                'first_image' => null,
                'page_id' => null,
            ];

            // Get first image
            if ($product->images) {
                $images = json_decode($product->images);
                $productData['first_image'] = $images[0] ?? null;
            }

            // Find page for this product
            $productPage = Page::where('product_id', $product->id)
                ->where('status', 'published')
                ->first();

            if ($productPage) {
                $productData['page_id'] = $productPage->id;
            } elseif ($order->landingPage) {
                $orderPage = Page::find($order->landing_page_id);
                if ($orderPage) {
                    $productData['page_id'] = $orderPage->id;
                }
            }

            $processedAdditionalProducts[] = $productData;
        }

        // Get landing page for back link
        $landingPage = null;
        if ($order->landing_page_id) {
            $landingPage = Page::find($order->landing_page_id);
        }

        $locale = app()->getLocale();
        $dir = $locale === 'ar' ? 'rtl' : 'ltr';

        return view('order.track', compact(
            'order',
            'locale',
            'dir',
            'additionalProducts',
            'processedAdditionalProducts',
            'additionalSalesEnabled',
            'size',
            'color',
            'landingPage'
        ));
    }

    /**
     * Generate unique 4-digit order number
     */
    protected function generateOrderNumber(): string
    {
        do {
            $orderNumber = str_pad((string)rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Prepare all template data to avoid @php blocks in Blade templates
     */
    protected function prepareTemplateData(Page $page): array
    {
        $settings = $page->settings ?? [];
        $smartCouponsEnabled = $settings['smart_coupons_enabled'] ?? false;

        // Calculate prices
        $originalPrice = $page->product ? ($page->product->price_cents / 100) : 0;
        $shippingPrice = $page->product ? (($page->product->shipping_price_cents ?? 0) / 100) : 0;
        $basePrice = $originalPrice + $shippingPrice;

        $displayPrice = $originalPrice;
        $increasedPrice = $originalPrice;
        $discountAmount = 0;
        $finalPrice = $originalPrice;

        if ($smartCouponsEnabled && $page->product) {
            // إضافة 25% على الإجمالي (السعر + الشحن)
            $increasedPrice = $basePrice * 1.25;
            // خصم 20% من السعر بعد الزيادة ليعيد السعر للإجمالي الأصلي
            $discountAmount = $increasedPrice * 0.20;
            $finalPrice = $increasedPrice - $discountAmount; // يجب أن يساوي $basePrice
            $displayPrice = $finalPrice;
            $discountPercent = 25;
        } else {
            // بدون smart coupons، حساب خصم مختلف حسب template
            // Default discount is 25% (0.75), but some templates use 0.77, 0.80, 0.85
            $discountMultiplier = 0.75; // Default
            $finalPrice = $originalPrice * $discountMultiplier;
            $displayPrice = $finalPrice;
            $discountPercent = round((($originalPrice - $finalPrice) / $originalPrice) * 100);
        }

        // Process images
        $images = $page->product && $page->product->images ? $page->product->images : [];
        $mainImage = !empty($images) ? $images[0] : null;
        $galleryImages = array_slice($images, 0, 6);
        $detailImage = !empty($images) ? $images[0] : null;
        $ogImage = !empty($images) ? $images[0] : null;

        // Generate Schema.org JSON-LD
        $schema = $this->generateSchema($page, $ogImage);

        // Get sizes and colors from settings
        $sizes = $settings['sizes'] ?? [];
        $colors = $settings['colors'] ?? [];
        $hasSizes = !empty($sizes);
        $hasColors = !empty($colors);

        // Process colors data
        $processedColors = [];
        foreach ($colors as $color) {
            $processedColors[] = array_merge($color, $this->processColorData($color));
        }

        // Page features settings
        $fixedOrderButtonEnabled = $settings['fixed_order_button_enabled'] ?? false;
        $remainingItemsCounterEnabled = $settings['remaining_items_counter_enabled'] ?? false;
        $buttonText = $settings['fixed_order_button_text'] ?? 'اطلب الآن';
        $buttonPosition = $settings['fixed_order_button_position'] ?? 'bottom-right';
        $buttonColor = $settings['fixed_order_button_color'] ?? '#667eea';
        $buttonShape = $settings['fixed_order_button_shape'] ?? 'rounded';
        $buttonSize = $settings['fixed_order_button_size'] ?? 'medium';
        $buttonIcon = $settings['fixed_order_button_icon'] ?? 'cart-shopping';
        $remainingItems = $settings['remaining_items'] ?? 10;

        // Field labels for custom forms
        $locale = app()->getLocale();
        $fieldLabels = [
            'full_name' => $locale === 'ar' ? 'الاسم الكامل' : 'Full Name',
            'email' => $locale === 'ar' ? 'البريد الإلكتروني' : 'Email',
            'phone' => $locale === 'ar' ? 'الهاتف' : 'Phone',
            'address' => $locale === 'ar' ? 'العنوان' : 'Address',
            'notes' => $locale === 'ar' ? 'ملاحظات' : 'Notes',
        ];

        return compact(
            'page',
            'settings',
            'smartCouponsEnabled',
            'originalPrice',
            'shippingPrice',
            'basePrice',
            'displayPrice',
            'increasedPrice',
            'discountAmount',
            'finalPrice',
            'discountPercent',
            'images',
            'mainImage',
            'galleryImages',
            'detailImage',
            'ogImage',
            'schema',
            'sizes',
            'colors',
            'processedColors',
            'hasSizes',
            'hasColors',
            'fixedOrderButtonEnabled',
            'remainingItemsCounterEnabled',
            'buttonText',
            'buttonPosition',
            'buttonColor',
            'buttonShape',
            'buttonSize',
            'buttonIcon',
            'remainingItems',
            'fieldLabels'
        );
    }

    /**
     * Process color data to determine if it's hex color or image URL
     */
    protected function processColorData(array $color): array
    {
        $colorValue = $color['value'] ?? $color['image'] ?? null;
        $isHexColor = false;
        $isImageUrl = false;

        if ($colorValue) {
            $trimmedValue = trim($colorValue);
            $isHexColor = preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $trimmedValue);
            $isImageUrl = str_starts_with($colorValue, 'http://') || str_starts_with($colorValue, 'https://');
        }

        return [
            'value' => $colorValue,
            'isHexColor' => $isHexColor,
            'isImageUrl' => $isImageUrl,
        ];
    }

    /**
     * Generate Schema.org JSON-LD for SEO
     */
    protected function generateSchema(Page $page, ?string $imageUrl = null): array
    {
        $schema = [
            "@context" => "https://schema.org",
            "@type" => "Product",
            "name" => $page->product->name ?? $page->title ?? 'Product',
            "description" => $page->seo_description ?? $page->content ?? ($page->product->description ?? ''),
        ];

        if ($imageUrl) {
            $schema["image"] = $imageUrl;
        }

        if ($page->product) {
            $originalPrice = $page->product->price_cents / 100;
            $schema["offers"] = [
                "@type" => "Offer",
                "url" => url()->current(),
                "priceCurrency" => $page->product->currency ?? 'EGP',
                "price" => number_format($originalPrice, 2),
                "availability" => "https://schema.org/InStock",
                "seller" => [
                    "@type" => "Organization",
                    "name" => $page->user->name ?? 'Sawa'
                ]
            ];
        }

        $schema["brand"] = [
            "@type" => "Brand",
            "name" => $page->user->name ?? 'Sawa'
        ];

        return $schema;
    }

    /**
     * Get template view path if available and enabled
     */
    protected function getTemplateView(Page $page): ?string
    {
        if (!$page->template || !$page->template->is_enabled || !$page->template->template_file) {
            return null;
        }

        $templatePath = 'landing-templates.' . str_replace('.blade.php', '', $page->template->template_file);

        return view()->exists($templatePath) ? $templatePath : null;
    }

    /**
     * Detect device type from user agent
     */
    protected function detectDeviceType(Request $request): string
    {
        $userAgent = $request->userAgent() ?? '';
        $userAgent = strtolower($userAgent);

        // Check for mobile devices
        if (preg_match('/mobile|android|iphone|ipod|blackberry|iemobile|opera mini/i', $userAgent)) {
            return 'mobile';
        }

        // Check for tablet devices
        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }

        // Default to desktop
        return 'desktop';
    }

    /**
     * Get location (city) from IP address
     * Note: This is a simple implementation. For production, consider using a geolocation service
     * Uses a timeout to prevent blocking the order submission
     */
    protected function getLocationFromIP(string $ip): ?string
    {
        // Skip localhost and private IPs
        if ($ip === '127.0.0.1' || $ip === '::1' || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return null;
        }

        try {
            // Use stream context with timeout to prevent blocking
            $context = stream_context_create([
                'http' => [
                    'timeout' => 2, // 2 second timeout
                    'ignore_errors' => true,
                ]
            ]);

            // Simple geolocation using ip-api.com (free, no API key required)
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=city", false, $context);
            if ($response) {
                $data = json_decode($response, true);
                return $data['city'] ?? null;
            }
        } catch (\Exception $e) {
            // Silently fail - location is optional and shouldn't block orders
            \Log::debug('Failed to get location from IP', ['ip' => $ip, 'error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Send Facebook Conversion API event if enabled
     *
     * @param Page $page
     * @param Order $order
     * @param Product $product
     * @param array $customerData
     * @param Request $request
     * @return void
     */
    protected function sendFacebookConversionAPIEvent(Page $page, Order $order, Product $product, array $customerData, Request $request): void
    {
        try {
            $pageOwner = $page->user;
            if (!$pageOwner) {
                return;
            }

            // Get user's subscription package
            $subscription = $pageOwner->subscription;
            $package = $subscription?->package;

            // Check if package has Facebook Conversion API feature
            if (!$package || !$package->hasFeature('facebook_conversion_api')) {
                return;
            }

            // Get user's Facebook Conversion API settings - هذه الإعدادات مربوطة تلقائياً بجميع صفحات الهبوط لنفس user_id
            $settings = FacebookConversionAPISetting::getForUser($pageOwner->id);

            // Check if Facebook Conversion API is enabled
            if (!$settings->is_enabled) {
                return;
            }

            // Get required credentials
            $pixelId = $settings->pixel_id;
            $accessToken = $settings->access_token;
            $testEventCode = $settings->test_event_code ?? null;
            $currency = $settings->currency ?? 'EGP';

            if (empty($pixelId) || empty($accessToken)) {
                \Log::warning('Facebook Conversion API credentials missing', [
                    'user_id' => $pageOwner->id,
                    'page_id' => $page->id,
                ]);
                return;
            }

            // Prepare user data
            $userData = [
                'email' => $customerData['email'] ?? null,
                'phone' => $customerData['phone'] ?? null,
                'ip' => $customerData['ip'] ?? $request->ip(),
                'user_agent' => $request->userAgent(),
            ];

            // Extract name if available
            $name = $customerData['name'] ?? '';
            if ($name) {
                $nameParts = explode(' ', $name, 2);
                $userData['first_name'] = $nameParts[0] ?? '';
                $userData['last_name'] = $nameParts[1] ?? '';
            }

            // Get Facebook cookies if available
            $userData['fbc'] = $request->cookie('_fbc');
            $userData['fbp'] = $request->cookie('_fbp');

            // Prepare order data - استخدام العملة من الإعدادات أو من الطلب
            $orderData = [
                'currency' => $currency ?? ($order->currency ?? 'EGP'),
                'value' => ($order->total_cents ?? 0) / 100,
                'content_ids' => [(string)($product->id ?? '')],
                'content_name' => $product->name ?? '',
                'content_type' => 'product',
                'num_items' => $order->quantity ?? 1,
                'event_source_url' => $request->url(),
            ];

            // Send event - الإعدادات مربوطة تلقائياً بجميع صفحات الهبوط لنفس user_id
            $service = new FacebookConversionAPIService();
            $service->sendPurchaseEvent($pixelId, $accessToken, $orderData, $userData, $testEventCode);
        } catch (\Exception $e) {
            // Don't fail the order if Facebook Conversion API fails
            \Log::error('Facebook Conversion API error', [
                'error' => $e->getMessage(),
                'page_id' => $page->id,
                'order_id' => $order->id ?? null,
            ]);
        }
    }

    /**
     * Send webhook event if webhooks are configured
     *
     * @param string $eventType
     * @param Order $order
     * @param Page $page
     * @param Product $product
     * @param array $customerData
     * @return void
     */
    protected function sendWebhookEvent(string $eventType, Order $order, Page $page, Product $product, array $customerData): void
    {
        try {
            $pageOwner = $page->user;
            if (!$pageOwner) {
                return;
            }

            // Prepare webhook payload
            $webhookData = [
                'order_id' => $order->order_number ?? 'ORD-' . $order->id,
                'order_internal_id' => $order->id,
                'customer_name' => $customerData['name'] ?? '',
                'customer_phone' => $customerData['phone'] ?? '',
                'customer_email' => $customerData['email'] ?? '',
                'customer_address' => $customerData['address'] ?? '',
                'product_name' => $product->name ?? '',
                'product_id' => $product->id,
                'quantity' => $order->quantity ?? 1,
                'total_amount' => ($order->total_cents ?? 0) / 100,
                'currency' => $order->currency ?? 'EGP',
                'landing_page' => $page->title ?? '',
                'landing_page_id' => $page->id,
                'order_status' => $order->status ?? 'pending',
                'shipping_status' => $order->shipping_status ?? 'pending',
                'created_at' => $order->created_at->toIso8601String(),
            ];

            // Add optional fields if available
            if (isset($customerData['size'])) {
                $webhookData['product_size'] = $customerData['size'];
            }
            if (isset($customerData['color'])) {
                $webhookData['product_color'] = $customerData['color'];
            }
            if (isset($customerData['city'])) {
                $webhookData['customer_city'] = $customerData['city'];
            }
            if (isset($customerData['device_type'])) {
                $webhookData['device_type'] = $customerData['device_type'];
            }

            // Send webhook event
            $webhookService = app(\App\Services\WebhookService::class);
            $webhookService->dispatch($eventType, $webhookData, $pageOwner->id);
        } catch (\Exception $e) {
            // Don't fail the order if webhook fails
            \Log::error('Webhook event error', [
                'error' => $e->getMessage(),
                'event_type' => $eventType,
                'page_id' => $page->id,
                'order_id' => $order->id ?? null,
            ]);
        }
    }
}
