<?php

namespace Tests\Unit;

use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user has subscription relationship
     */
    public function test_user_has_subscription_relationship(): void
    {
        $user = User::factory()->create();
        $package = SubscriptionPackage::create([
            'name' => 'Test Package',
            'slug' => 'test',
            'price_cents' => 0,
            'is_free' => true,
            'interval' => 'monthly',
        ]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => 'active',
        ]);

        $this->assertInstanceOf(Subscription::class, $user->subscription);
        $this->assertEquals($subscription->id, $user->subscription->id);
    }

    /**
     * Test subscription belongs to user
     */
    public function test_subscription_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $package = SubscriptionPackage::create([
            'name' => 'Test Package',
            'slug' => 'test',
            'price_cents' => 0,
            'is_free' => true,
            'interval' => 'monthly',
        ]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => 'active',
        ]);

        $this->assertInstanceOf(User::class, $subscription->user);
        $this->assertEquals($user->id, $subscription->user->id);
    }

    /**
     * Test subscription belongs to package
     */
    public function test_subscription_belongs_to_package(): void
    {
        $user = User::factory()->create();
        $package = SubscriptionPackage::create([
            'name' => 'Test Package',
            'slug' => 'test',
            'price_cents' => 0,
            'is_free' => true,
            'interval' => 'monthly',
        ]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => 'active',
        ]);

        $this->assertInstanceOf(SubscriptionPackage::class, $subscription->package);
        $this->assertEquals($package->id, $subscription->package->id);
    }

    /**
     * Test user isAdmin method
     */
    public function test_user_isAdmin_returns_true_for_admin_role(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    /**
     * Test user isAdmin returns false for non-admin role
     */
    public function test_user_isAdmin_returns_false_for_non_admin_role(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->assertFalse($user->isAdmin());
    }

    /**
     * Test package hasFeature method
     */
    public function test_package_hasFeature_returns_true_when_feature_enabled(): void
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
    }
}
