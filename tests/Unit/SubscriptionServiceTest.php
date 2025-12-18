<?php

namespace Tests\Unit;

use App\Models\Subscription;
use App\Models\SubscriptionPackage;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;
use Carbon\Carbon;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SubscriptionService();
    }

    /**
     * Test subscribing user to free package creates active subscription
     * 
     * Free packages should immediately activate without payment processing.
     * Subscription status should be 'active' and starts_at should be set.
     */
    public function test_subscribe_user_to_free_package_creates_active_subscription(): void
    {
        $user = User::factory()->create();
        $package = SubscriptionPackage::create([
            'name' => 'Free Package',
            'slug' => 'free',
            'price_cents' => 0,
            'is_free' => true,
            'interval' => 'monthly',
            'pages_limit' => 5,
            'products_limit' => 10,
        ]);

        $subscription = $this->service->subscribeUserToPackage($user, $package->id);

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals($user->id, $subscription->user_id);
        $this->assertEquals($package->id, $subscription->package_id);
        $this->assertEquals('active', $subscription->status);
        $this->assertNotNull($subscription->starts_at);
        $this->assertNull($subscription->ends_at);
    }

    /**
     * Test subscribing user to paid package creates pending subscription
     * 
     * Paid packages require payment before activation.
     * Subscription status should be 'pending' until payment is verified.
     */
    public function test_subscribe_user_to_paid_package_creates_pending_subscription(): void
    {
        $user = User::factory()->create();
        $package = SubscriptionPackage::create([
            'name' => 'Paid Package',
            'slug' => 'paid',
            'price_cents' => 9900,
            'is_free' => false,
            'interval' => 'monthly',
            'pages_limit' => 50,
            'products_limit' => 100,
        ]);

        $subscription = $this->service->subscribeUserToPackage($user, $package->id);

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals($user->id, $subscription->user_id);
        $this->assertEquals($package->id, $subscription->package_id);
        $this->assertEquals('pending', $subscription->status);
        $this->assertNull($subscription->starts_at);
        $this->assertNull($subscription->ends_at);
    }

    /**
     * Test subscribing to non-existent package throws exception
     */
    public function test_subscribe_to_nonexistent_package_throws_exception(): void
    {
        $user = User::factory()->create();

        $this->expectException(ModelNotFoundException::class);
        $this->service->subscribeUserToPackage($user, 99999);
    }

    /**
     * Test subscribing using package slug works
     */
    public function test_subscribe_using_package_slug_works(): void
    {
        $user = User::factory()->create();
        $package = SubscriptionPackage::create([
            'name' => 'Free Package',
            'slug' => 'free-package',
            'price_cents' => 0,
            'is_free' => true,
            'interval' => 'monthly',
            'pages_limit' => 5,
            'products_limit' => 10,
        ]);

        $subscription = $this->service->subscribeUserToPackage($user, 'free-package');

        $this->assertInstanceOf(Subscription::class, $subscription);
        $this->assertEquals($package->id, $subscription->package_id);
    }
}
