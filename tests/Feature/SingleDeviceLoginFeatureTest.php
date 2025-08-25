<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class SingleDeviceLoginFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_user_can_login_with_email(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post(route('filament.admin.auth.login'), [
            'login' => 'test@example.com',
            'password' => 'password'
        ]);

        $this->assertAuthenticated();
        
        // Verify device token was set
        $user->refresh();
        $this->assertNotNull($user->device_token);
        $this->assertNotNull($user->device_info);
        $this->assertNotNull($user->last_login_at);
    }

    public function test_user_can_login_with_username(): void
    {
        $user = User::factory()->create([
            'name' => 'testuser',
            'password' => bcrypt('password')
        ]);

        $response = $this->post(route('filament.admin.auth.login'), [
            'login' => 'testuser',
            'password' => 'password'
        ]);

        $this->assertAuthenticated();
        
        // Verify device token was set
        $user->refresh();
        $this->assertNotNull($user->device_token);
        $this->assertNotNull($user->device_info);
    }

    public function test_second_login_invalidates_first_device(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // First login - simulate first browser
        $firstSession = $this->session([]);
        $response1 = $firstSession->post(route('filament.admin.auth.login'), [
            'login' => 'test@example.com',
            'password' => 'password'
        ]);

        $user->refresh();
        $firstDeviceToken = $user->device_token;

        // Second login - simulate second browser  
        $secondSession = $this->session([]);
        $response2 = $secondSession->post(route('filament.admin.auth.login'), [
            'login' => 'test@example.com',
            'password' => 'password'
        ]);

        $user->refresh();
        $secondDeviceToken = $user->device_token;

        // Tokens should be different
        $this->assertNotEquals($firstDeviceToken, $secondDeviceToken);

        // First device token should no longer be valid
        $this->assertFalse($user->isDeviceAllowed($firstDeviceToken));
        $this->assertTrue($user->isDeviceAllowed($secondDeviceToken));
    }

    public function test_middleware_blocks_access_with_invalid_device_token(): void
    {
        $user = User::factory()->create();
        
        // Login user manually without proper device token setup
        $this->actingAs($user);
        
        // Try to access admin dashboard without device token in session
        $response = $this->get('/admin');
        
        // Should redirect to login
        $response->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_middleware_blocks_access_with_mismatched_device_token(): void
    {
        $user = User::factory()->create();
        $user->setDeviceToken('valid-user-agent', '127.0.0.1');
        
        $this->actingAs($user);
        
        // Set wrong device token in session
        Session::put('device_token', 'invalid_token');
        
        $response = $this->get('/admin');
        
        // Should redirect to login with error
        $response->assertRedirect(route('filament.admin.auth.login'));
    }

    public function test_valid_device_token_allows_access(): void
    {
        $user = User::factory()->create();
        $user->setDeviceToken('valid-user-agent', '127.0.0.1');
        
        $this->actingAs($user);
        Session::put('device_token', $user->device_token);
        
        $response = $this->get('/admin');
        
        // Should allow access
        $response->assertOk();
    }

    public function test_logout_clears_device_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        // Login first
        $this->post(route('filament.admin.auth.login'), [
            'login' => 'test@example.com',
            'password' => 'password'
        ]);

        $user->refresh();
        $this->assertNotNull($user->device_token);

        // Logout
        $response = $this->post('/admin/logout');

        $user->refresh();
        $this->assertNull($user->device_token);
        $this->assertNull($user->device_info);
    }

    public function test_login_records_device_information(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $customUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
        
        $response = $this->withHeaders([
            'User-Agent' => $customUserAgent
        ])->post(route('filament.admin.auth.login'), [
            'login' => 'test@example.com',
            'password' => 'password'
        ]);

        $user->refresh();
        $this->assertNotNull($user->device_info);
        $this->assertNotNull($user->last_login_at);
        
        $deviceInfo = json_decode($user->device_info, true);
        $this->assertEquals('Chrome', $deviceInfo['browser']);
        $this->assertEquals('Windows', $deviceInfo['os']);
        $this->assertEquals($customUserAgent, $deviceInfo['user_agent']);
    }

    public function test_invalid_login_credentials_fail(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $response = $this->post(route('filament.admin.auth.login'), [
            'login' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $this->assertGuest();
        
        $user->refresh();
        $this->assertNull($user->device_token);
    }
}
