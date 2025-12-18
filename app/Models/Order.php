<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'status',
        'shipping_status',
        'total_cents',
        'currency',
        'landing_page_id',
        'order_number',
        'customer_data',
    ];

    protected $casts = [
        'customer_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function landingPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'landing_page_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(\App\Models\Message::class);
    }
}
