<?php

namespace App\Http\Controllers\Admin;

use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;

class PackageController extends BaseAdminController
{
    public function index()
    {
        $packages = SubscriptionPackage::orderBy('price_cents')->paginate(15);
        $this->addFeaturesArrayToPackages($packages);

        return view('admin.packages.index', $this->getViewData(compact('packages')));
    }

    /**
     * Add features_array to packages collection
     */
    protected function addFeaturesArrayToPackages($packages): void
    {
        $packages->getCollection()->transform(function ($package) {
            $package->features_array = $package->features ?? [];
            return $package;
        });
    }

    public function create()
    {
        return view('admin.packages.form', $this->getViewData([
            'package' => new SubscriptionPackage(),
        ]));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        SubscriptionPackage::create($data);

        return $this->redirectWithSuccess('admin.packages.index', 'messages.package_created');
    }

    public function edit(SubscriptionPackage $package)
    {
        return view('admin.packages.form', $this->getViewData(compact('package')));
    }

    public function update(Request $request, SubscriptionPackage $package)
    {
        $data = $this->validateData($request);
        $package->update($data);

        return $this->redirectWithSuccess('admin.packages.index', 'messages.package_updated');
    }

    public function destroy(SubscriptionPackage $package)
    {
        $package->delete();

        return $this->redirectWithSuccess('admin.packages.index', 'messages.package_deleted');
    }

    /**
     * Validate package data
     */
    protected function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:subscription_packages,slug,' . $request->route('package')?->id],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_free' => ['sometimes', 'boolean'],
            'interval' => ['nullable', 'string', 'max:64'],
            'pages_limit' => ['nullable', 'integer', 'min:0'],
            'products_limit' => ['nullable', 'integer', 'min:0'],
            'daily_orders_limit' => ['nullable', 'integer', 'min:0'],
            'monthly_orders_limit' => ['nullable', 'integer', 'min:0'],
            'monthly_support_tickets_limit' => ['nullable', 'integer', 'min:0'],
            'features' => ['nullable', 'array'],
            'features.google_analytics' => ['sometimes', 'boolean'],
            'features.facebook_pixel' => ['sometimes', 'boolean'],
            'features.facebook_conversion_api' => ['sometimes', 'boolean'],
            'features.tiktok_pixel' => ['sometimes', 'boolean'],
            'features.snapchat_pixel' => ['sometimes', 'boolean'],
        ]);

        // Save price directly in EGP (no conversion to cents)
        if (isset($validated['price'])) {
            $validated['price_cents'] = (int) $validated['price'];
            unset($validated['price']);
        } else {
            $validated['price_cents'] = 0;
        }

        // If package is free, set price_cents to 0
        if ($request->has('is_free') && $request->input('is_free')) {
            $validated['price_cents'] = 0;
        }

        $validated['features'] = $this->prepareFeatures($request, $validated);

        return $validated;
    }

    /**
     * Prepare features array from request
     */
    protected function prepareFeatures(Request $request, array $validated): array
    {
        return [
            'description' => $validated['features']['description'] ?? '',
            'google_analytics' => $request->has('features.google_analytics'),
            'facebook_pixel' => $request->has('features.facebook_pixel'),
            'facebook_conversion_api' => $request->has('features.facebook_conversion_api'),
            'tiktok_pixel' => $request->has('features.tiktok_pixel'),
            'snapchat_pixel' => $request->has('features.snapchat_pixel'),
        ];
    }
}
