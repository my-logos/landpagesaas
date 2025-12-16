<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class AdditionalSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        // AI Settings
        'ai_provider',
        'ai_gemini_api_key',
        'ai_openai_api_key',
        'ai_openai_model',
        // Pixels & Analytics
        'facebook_pixel_enabled',
        'facebook_pixel_id',
        'facebook_conversion_api_enabled',
        'facebook_conversion_api_access_token',
        'facebook_conversion_api_pixel_id',
        'facebook_conversion_api_test_event_code',
        'facebook_conversion_api_currency',
        'google_analytics_enabled',
        'google_analytics_id',
        'tiktok_pixel_enabled',
        'tiktok_pixel_id',
        'snapchat_pixel_enabled',
        'snapchat_pixel_id',
        'google_tag_enabled',
        'google_tag_id',
        // Security
        'recaptcha_version',
        'recaptcha_v2_enabled',
        'recaptcha_v2_site_key',
        'recaptcha_v2_secret_key',
        'recaptcha_v3_enabled',
        'recaptcha_v3_site_key',
        'recaptcha_v3_secret_key',
        // Loading Bar
        'loading_bar_enabled',
    ];

    protected $casts = [
        'facebook_pixel_enabled' => 'boolean',
        'facebook_conversion_api_enabled' => 'boolean',
        'google_analytics_enabled' => 'boolean',
        'tiktok_pixel_enabled' => 'boolean',
        'snapchat_pixel_enabled' => 'boolean',
        'google_tag_enabled' => 'boolean',
        'recaptcha_v2_enabled' => 'boolean',
        'recaptcha_v3_enabled' => 'boolean',
        'loading_bar_enabled' => 'boolean',
    ];

    /**
     * Get the user that owns the settings
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create settings for a user
     */
    public static function getForUser(int $userId): self
    {
        // Ensure userId is integer
        $userId = (int) $userId;

        $settings = self::where('user_id', $userId)->first();

        if (!$settings) {
            $settings = self::create([
                'user_id' => $userId,
                'ai_provider' => 'gemini',
                'ai_openai_model' => 'gpt-3.5-turbo',
                'loading_bar_enabled' => true,
            ]);
        }

        return $settings;
    }

    /**
     * Get value for a setting key (for admin/global settings)
     * First tries to get from global settings (user_id = null), 
     * then from any available settings if global not found
     */
    public static function getValue(string $key, $default = null)
    {
        // First, try to get admin/global settings (user_id = null)
        $settings = self::whereNull('user_id')->first();

        // If no global settings found or the key value is empty, try to get any settings that have a non-empty value for this key
        // This handles cases where settings are saved for a specific admin user
        if (!$settings || empty($settings->$key)) {
            $settings = self::whereNotNull($key)
                ->where($key, '!=', '')
                ->orderBy('id', 'desc') // Get the most recent one
                ->first();
        }

        // If still no settings found, return default
        if (!$settings) {
            return $default;
        }

        $value = $settings->$key ?? null;

        // Return value if it's not empty, otherwise return default
        return !empty($value) ? $value : $default;
    }
}
