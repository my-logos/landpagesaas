<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingPageTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'preview_image',
        'template_file',
        'is_enabled',
        'settings',
        'sort_order',
        'category',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'settings' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Get all pages using this template
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'template_id');
    }

    /**
     * Get enabled templates
     */
    public static function getEnabled()
    {
        return self::where('is_enabled', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get template file path
     */
    public function getTemplateFilePath(): ?string
    {
        if (!$this->template_file) {
            return null;
        }

        // Check if file exists in resources/views/landing-templates/
        $path = resource_path('views/landing-templates/' . $this->template_file);
        return file_exists($path) ? $path : null;
    }

    /**
     * Get preview image URL
     */
    public function getPreviewImageUrl(): ?string
    {
        if (!$this->preview_image) {
            return null;
        }

        return asset('storage/templates/' . $this->preview_image);
    }
}
