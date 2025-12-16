<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_enabled',
        'credentials',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'credentials' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Get credentials for a specific gateway
     */
    public static function getCredentials(string $code): array
    {
        $gateway = self::where('code', $code)
            ->where('is_enabled', true)
            ->first();

        if (!$gateway) {
            return [];
        }

        return $gateway->credentials ?? [];
    }

    /**
     * Get all enabled gateways
     */
    public static function getEnabled(): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('is_enabled', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
