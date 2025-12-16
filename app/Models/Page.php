<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'template_id',
        'title',
        'slug',
        'status',
        'generation_status',
        'generation_error',
        'store_name',
        'custom_domain',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'content',
        'additional_description',
        'ai_version',
        'form_type',
        'form_fields',
        'facebook_pixel',
        'tiktok_pixel',
        'snapchat_pixel',
        'google_analytics_id',
        'settings',
    ];

    protected $casts = [
        'form_fields' => 'array',
        'settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(LandingPageTemplate::class, 'template_id');
    }
}
