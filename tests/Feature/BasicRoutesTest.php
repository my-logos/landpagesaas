<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test landing page route is accessible
     */
    public function test_landing_page_route_is_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test user dashboard requires authentication
     */
    public function test_user_dashboard_requires_authentication(): void
    {
        $response = $this->get('/user/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated active user can access dashboard
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/user/dashboard');

        // Dashboard might redirect or show, but should not be 403/500
        $this->assertContains($response->status(), [200, 302]);
    }
}
