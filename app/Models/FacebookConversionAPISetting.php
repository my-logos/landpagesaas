<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacebookConversionAPISetting extends Model
{
    use HasFactory;

    protected $table = 'facebook_conversion_api_settings';

    /**
     * Primary key is now 'id' (standard Laravel convention)
     */
    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'is_enabled',
        'pixel_id',
        'access_token',
        'test_event_code',
        'currency',
        'is_system_default',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'is_system_default' => 'boolean',
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
        $userId = (int) $userId;

        $settings = self::where('user_id', $userId)->first();

        if (!$settings) {
            $settings = self::create([
                'user_id' => $userId,
                'is_enabled' => false,
                'currency' => 'EGP',
            ]);
        }

        return $settings;
    }

    /**
     * Get system default settings (for admin/system landing page)
     */
    public static function getSystemDefault(): ?self
    {
        return self::where('is_system_default', true)
            ->whereNull('user_id')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Check if settings are connected (have pixel_id and access_token)
     */
    public function isConnected(): bool
    {
        return $this->is_enabled && !empty($this->pixel_id) && !empty($this->access_token);
    }
}
