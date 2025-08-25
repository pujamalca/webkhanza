<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeviceTokenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_user_can_generate_device_token(): void
    {
        $user = User::factory()->create();
        $token = $user->generateDeviceToken();
        
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token)); // SHA256 hash length
        $this->assertNotEmpty($token);
    }

    public function test_user_can_set_device_token(): void
    {
        $user = User::factory()->create();
        $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $ipAddress = '192.168.1.1';

        $user->setDeviceToken($userAgent, $ipAddress);

        $this->assertNotNull($user->device_token);
        $this->assertNotNull($user->device_info);
        $this->assertNotNull($user->last_login_at);
        $this->assertEquals($ipAddress, $user->last_login_ip);
        
        // Test device info JSON
        $deviceInfo = json_decode($user->device_info, true);
        $this->assertEquals('Chrome', $deviceInfo['browser']);
        $this->assertEquals('Windows', $deviceInfo['os']);
        $this->assertEquals($userAgent, $deviceInfo['user_agent']);
    }

    public function test_user_can_check_device_allowed(): void
    {
        $user = User::factory()->create();
        $token = $user->generateDeviceToken();
        
        $user->update(['device_token' => $token]);
        
        $this->assertTrue($user->isDeviceAllowed($token));
        $this->assertFalse($user->isDeviceAllowed('invalid_token'));
        $this->assertFalse($user->isDeviceAllowed(''));
    }

    public function test_user_can_logout_from_all_devices(): void
    {
        $user = User::factory()->create();
        $user->setDeviceToken('test-agent', '127.0.0.1');
        
        $this->assertNotNull($user->device_token);
        $this->assertNotNull($user->device_info);
        
        $user->logoutFromAllDevices();
        
        $this->assertNull($user->device_token);
        $this->assertNull($user->device_info);
    }

    public function test_browser_detection(): void
    {
        $user = User::factory()->create();
        
        $testCases = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36' => 'Chrome',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0' => 'Firefox',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15' => 'Safari',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36 Edg/91.0.864.59' => 'Edge',
        ];

        foreach ($testCases as $userAgent => $expectedBrowser) {
            $user->setDeviceToken($userAgent, '127.0.0.1');
            $deviceInfo = json_decode($user->device_info, true);
            $this->assertEquals($expectedBrowser, $deviceInfo['browser']);
        }
    }

    public function test_os_detection(): void
    {
        $user = User::factory()->create();
        
        $testCases = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36' => 'Windows',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36' => 'macOS',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36' => 'Linux',
            'Mozilla/5.0 (Linux; Android 11) AppleWebKit/537.36' => 'Android',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15' => 'iOS',
        ];

        foreach ($testCases as $userAgent => $expectedOS) {
            $user->setDeviceToken($userAgent, '127.0.0.1');
            $deviceInfo = json_decode($user->device_info, true);
            $this->assertEquals($expectedOS, $deviceInfo['os']);
        }
    }

    public function test_device_token_uniqueness(): void
    {
        $user = User::factory()->create();
        
        $token1 = $user->generateDeviceToken();
        $token2 = $user->generateDeviceToken();
        
        $this->assertNotEquals($token1, $token2);
    }
}
