<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'short_description',
        'price_cents',
        'shipping_price_cents',
        'currency',
        'ai_version',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
        'price_cents' => 'integer',
        'shipping_price_cents' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function pages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Check if product has any landing pages
     */
    public function hasLandingPages(): bool
    {
        return $this->pages()->exists();
    }

    /**
     * Get the first landing page for this product
     */
    public function getFirstLandingPage()
    {
        return $this->pages()->first();
    }
}
