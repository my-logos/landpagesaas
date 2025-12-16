<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get existing data
        $existingData = DB::table('additional_settings')->get();

        // Drop old table
        Schema::dropIfExists('additional_settings');

        // Create new table with columns structure
        Schema::create('additional_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            // AI Settings
            $table->string('ai_provider')->default('gemini')->nullable();
            $table->text('ai_gemini_api_key')->nullable();
            $table->text('ai_openai_api_key')->nullable();
            $table->string('ai_openai_model')->default('gpt-3.5-turbo')->nullable();

            // Pixels & Analytics
            $table->boolean('facebook_pixel_enabled')->default(false);
            $table->string('facebook_pixel_id')->nullable();
            $table->boolean('google_analytics_enabled')->default(false);
            $table->string('google_analytics_id')->nullable();
            $table->boolean('tiktok_pixel_enabled')->default(false);
            $table->string('tiktok_pixel_id')->nullable();
            $table->boolean('snapchat_pixel_enabled')->default(false);
            $table->string('snapchat_pixel_id')->nullable();
            $table->boolean('google_tag_enabled')->default(false);
            $table->string('google_tag_id')->nullable();

            // Security
            $table->boolean('recaptcha_v2_enabled')->default(false);
            $table->string('recaptcha_v2_site_key')->nullable();
            $table->string('recaptcha_v2_secret_key')->nullable();
            $table->boolean('recaptcha_v3_enabled')->default(false);
            $table->string('recaptcha_v3_site_key')->nullable();
            $table->string('recaptcha_v3_secret_key')->nullable();

            // Loading Bar
            $table->boolean('loading_bar_enabled')->default(true);

            $table->timestamps();

            // Unique constraint: one row per user (or null for global admin settings)
            $table->unique('user_id');
        });

        // Migrate existing data to new structure (for admin/global settings where user_id is null)
        if ($existingData->isNotEmpty()) {
            $adminSettings = [
                'ai_provider' => 'gemini',
                'ai_gemini_api_key' => null,
                'ai_openai_api_key' => null,
                'ai_openai_model' => 'gpt-3.5-turbo',
                'facebook_pixel_enabled' => false,
                'facebook_pixel_id' => null,
                'google_analytics_enabled' => false,
                'google_analytics_id' => null,
                'tiktok_pixel_enabled' => false,
                'tiktok_pixel_id' => null,
                'snapchat_pixel_enabled' => false,
                'snapchat_pixel_id' => null,
                'google_tag_enabled' => false,
                'google_tag_id' => null,
                'recaptcha_v2_enabled' => false,
                'recaptcha_v2_site_key' => null,
                'recaptcha_v2_secret_key' => null,
                'recaptcha_v3_enabled' => false,
                'recaptcha_v3_site_key' => null,
                'recaptcha_v3_secret_key' => null,
                'loading_bar_enabled' => true,
            ];

            foreach ($existingData as $setting) {
                $key = $setting->key;
                $value = $setting->value;

                // Map old key-value to new column structure
                switch ($key) {
                    case 'ai_provider':
                        $adminSettings['ai_provider'] = $value ?? 'gemini';
                        break;
                    case 'ai_gemini_api_key':
                        $adminSettings['ai_gemini_api_key'] = $value;
                        break;
                    case 'ai_openai_api_key':
                        $adminSettings['ai_openai_api_key'] = $value;
                        break;
                    case 'ai_openai_model':
                        $adminSettings['ai_openai_model'] = $value ?? 'gpt-3.5-turbo';
                        break;
                    case 'facebook_pixel_enabled':
                        $adminSettings['facebook_pixel_enabled'] = ($value === '1' || $value === 1);
                        break;
                    case 'facebook_pixel_id':
                        $adminSettings['facebook_pixel_id'] = $value;
                        break;
                    case 'google_analytics_enabled':
                        $adminSettings['google_analytics_enabled'] = ($value === '1' || $value === 1);
                        break;
                    case 'google_analytics_id':
                        $adminSettings['google_analytics_id'] = $value;
                        break;
                    case 'tiktok_pixel_enabled':
                        $adminSettings['tiktok_pixel_enabled'] = ($value === '1' || $value === 1);
                        break;
                    case 'tiktok_pixel_id':
                        $adminSettings['tiktok_pixel_id'] = $value;
                        break;
                    case 'snapchat_pixel_enabled':
                        $adminSettings['snapchat_pixel_enabled'] = ($value === '1' || $value === 1);
                        break;
                    case 'snapchat_pixel_id':
                        $adminSettings['snapchat_pixel_id'] = $value;
                        break;
                    case 'google_tag_enabled':
                        $adminSettings['google_tag_enabled'] = ($value === '1' || $value === 1);
                        break;
                    case 'google_tag_id':
                        $adminSettings['google_tag_id'] = $value;
                        break;
                    case 'recaptcha_v2_enabled':
                        $adminSettings['recaptcha_v2_enabled'] = ($value === '1' || $value === 1);
                        break;
                    case 'recaptcha_v2_site_key':
                        $adminSettings['recaptcha_v2_site_key'] = $value;
                        break;
                    case 'recaptcha_v2_secret_key':
                        $adminSettings['recaptcha_v2_secret_key'] = $value;
                        break;
                    case 'recaptcha_v3_enabled':
                        $adminSettings['recaptcha_v3_enabled'] = ($value === '1' || $value === 1);
                        break;
                    case 'recaptcha_v3_site_key':
                        $adminSettings['recaptcha_v3_site_key'] = $value;
                        break;
                    case 'recaptcha_v3_secret_key':
                        $adminSettings['recaptcha_v3_secret_key'] = $value;
                        break;
                    case 'loading_bar_enabled':
                        $adminSettings['loading_bar_enabled'] = ($value === '1' || $value === 1 || $value === null);
                        break;
                }
            }

            // Insert admin settings (user_id = null for global admin settings)
            DB::table('additional_settings')->insert(array_merge($adminSettings, [
                'user_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_settings');

        // Recreate old structure
        Schema::create('additional_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
