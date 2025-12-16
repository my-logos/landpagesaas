<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'locale',
    ];

    /**
     * Get setting value by key (backward compatibility)
     *
     * @param string $key
     * @param mixed $default
     * @param string|null $locale
     * @return mixed
     */
    public static function get(string $key, $default = null, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $setting = self::where('key', $key)
            ->where('locale', $locale)
            ->first();

        if (!$setting && $locale !== 'en') {
            $setting = self::where('key', $key)
                ->where('locale', 'en')
                ->first();
        }

        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key (backward compatibility)
     *
     * @param string $key
     * @param mixed $value
     * @param string|null $locale
     * @return void
     */
    public static function set(string $key, $value, ?string $locale = null): void
    {
        $locale = $locale ?? app()->getLocale();
        self::updateOrCreate(
            ['key' => $key, 'locale' => $locale],
            ['value' => $value]
        );
    }

    /**
     * Get all settings as array (backward compatibility)
     *
     * @param string|null $locale
     * @return array
     */
    public static function allAsArray(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        return self::where('locale', $locale)
            ->pluck('value', 'key')
            ->toArray();
    }
}
