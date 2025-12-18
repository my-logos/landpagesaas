<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutesTest extends TestCase
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
     * 
     * Unauthenticated users should be redirected to login page
     */
    public function test_user_dashboard_requires_authentication(): void
    {
        $response = $this->get('/user/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test authenticated user can access dashboard
     * 
     * Active users with 'user' role should be able to access their dashboard
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/user/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test admin dashboard requires admin role
     * 
     * Regular users should be denied access (403 Forbidden) to admin routes
     */
    public function test_admin_dashboard_requires_admin_role(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Test admin can access admin dashboard
     * 
     * Users with 'admin' role should have full access to admin routes
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_active' => true,
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test inactive user cannot access protected routes
     * 
     * Inactive users are blocked from all protected routes, even if authenticated.
     * This prevents access by users awaiting activation or suspended accounts.
     */
    public function test_inactive_user_cannot_access_protected_routes(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get('/user/dashboard');

        $response->assertStatus(403);
    }
}
