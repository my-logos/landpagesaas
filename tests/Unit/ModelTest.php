<?php

namespace Tests\Unit;

use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user model can be created
     */
    public function test_user_model_can_be_created(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    /**
     * Test subscription package model can be created
     */
    public function test_subscription_package_model_can_be_created(): void
    {
        $package = SubscriptionPackage::create([
            'name' => 'Test Package',
            'slug' => 'test-package',
            'price_cents' => 9900,
            'is_free' => false,
            'interval' => 'monthly',
            'pages_limit' => 10,
            'products_limit' => 20,
        ]);

        $this->assertDatabaseHas('subscription_packages', [
            'name' => 'Test Package',
            'slug' => 'test-package',
        ]);

        $this->assertEquals('Test Package', $package->name);
        $this->assertEquals(9900, $package->price_cents);
        $this->assertFalse($package->is_free);
    }

    /**
     * Test package hasFeature method
     */
    public function test_package_hasFeature_method_works(): void
    {
        $package = SubscriptionPackage::create([
            'name' => 'Test Package',
            'slug' => 'test',
            'price_cents' => 0,
            'is_free' => true,
            'interval' => 'monthly',
            'features' => [
                'google_analytics' => true,
                'facebook_pixel' => false,
            ],
        ]);

        $this->assertTrue($package->hasFeature('google_analytics'));
        $this->assertFalse($package->hasFeature('facebook_pixel'));
        $this->assertFalse($package->hasFeature('nonexistent_feature'));
    }

    /**
     * Test user isAdmin method
     */
    public function test_user_isAdmin_method_works(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());
    }
}
