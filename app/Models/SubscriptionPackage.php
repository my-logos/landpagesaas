<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price_cents',
        'is_free',
        'interval', // monthly, yearly
        'pages_limit',
        'products_limit',
        'daily_orders_limit',
        'monthly_orders_limit',
        'features', // json
    ];

    protected $casts = [
        'features' => 'array',
        'is_free' => 'bool',
        'price_cents' => 'integer',
        'pages_limit' => 'integer',
        'products_limit' => 'integer',
        'daily_orders_limit' => 'integer',
        'monthly_orders_limit' => 'integer',
    ];

    /**
     * Check if package has a specific feature enabled
     */
    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        return isset($features[$feature]) && $features[$feature] === true;
    }

    /**
     * Get all enabled features
     */
    public function getEnabledFeatures(): array
    {
        $features = $this->features ?? [];
        return array_filter($features, function($value, $key) {
            return $value === true && in_array($key, ['google_analytics', 'facebook_pixel', 'facebook_conversion_api', 'tiktok_pixel', 'snapchat_pixel']);
        }, ARRAY_FILTER_USE_BOTH);
    }
}
