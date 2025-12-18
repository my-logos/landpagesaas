<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test home page loads successfully
     */
    public function test_home_page_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test login page loads successfully
     */
    public function test_login_page_returns_successful_response(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Test registration step 1 page loads successfully
     */
    public function test_registration_step1_page_returns_successful_response(): void
    {
        $response = $this->get('/register/step1');

        $response->assertStatus(200);
    }

    /**
     * Test user can login with valid credentials
     * Note: reCAPTCHA validation is disabled in testing environment
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test user cannot login with invalid credentials
     * 
     * LoginController uses flash messages ('error') instead of validation errors.
     * Note: reCAPTCHA validation is disabled in test environment
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // LoginController uses flash message 'error' instead of validation errors
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    /**
     * Test inactive user cannot login
     * 
     * Inactive users are blocked from accessing the system even with correct credentials.
     * LoginController uses flash messages ('error') to communicate the issue.
     * Note: reCAPTCHA validation is disabled in test environment
     */
    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // LoginController uses flash message 'error' for inactive users
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    /**
     * Test authenticated user can logout
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect();
        $this->assertGuest();
    }
}
