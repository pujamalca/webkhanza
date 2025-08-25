<?php

namespace Tests\Unit;

use App\Http\Middleware\SingleDeviceLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class SingleDeviceLoginMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected SingleDeviceLogin $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new SingleDeviceLogin();
    }

    public function test_middleware_allows_unauthenticated_users(): void
    {
        $request = Request::create('/admin/login');
        
        $response = $this->middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
    }

    public function test_middleware_redirects_when_no_device_token_in_session(): void
    {
        $user = User::factory()->create();
        Auth::login($user);
        
        $request = Request::create('/admin');
        
        $response = $this->middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('login', $response->headers->get('Location'));
        $this->assertGuest();
    }

    public function test_middleware_redirects_when_device_token_mismatch(): void
    {
        $user = User::factory()->create();
        $user->setDeviceToken('valid-agent', '127.0.0.1');
        Auth::login($user);
        
        // Set wrong token in session
        Session::put('device_token', 'wrong_token');
        
        $request = Request::create('/admin');
        
        $response = $this->middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertGuest();
    }

    public function test_middleware_allows_valid_device_token(): void
    {
        $user = User::factory()->create();
        $user->setDeviceToken('valid-agent', '127.0.0.1');
        Auth::login($user);
        
        // Set correct token in session
        Session::put('device_token', $user->device_token);
        
        $request = Request::create('/admin');
        
        $response = $this->middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals('OK', $response->getContent());
        $this->assertAuthenticated();
    }

    public function test_middleware_handles_user_without_device_token(): void
    {
        $user = User::factory()->create();
        // User has no device token set
        Auth::login($user);
        
        $request = Request::create('/admin');
        
        $response = $this->middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertGuest();
    }

    public function test_middleware_logs_out_and_clears_session_on_mismatch(): void
    {
        $user = User::factory()->create();
        $user->setDeviceToken('valid-agent', '127.0.0.1');
        Auth::login($user);
        
        Session::put('device_token', 'invalid_token');
        Session::put('some_data', 'should_be_cleared');
        
        $request = Request::create('/admin');
        
        $this->middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertGuest();
        $this->assertNull(Session::get('some_data'));
    }

    public function test_middleware_works_with_null_device_token(): void
    {
        $user = User::factory()->create(['device_token' => null]);
        Auth::login($user);
        
        Session::put('device_token', 'some_token');
        
        $request = Request::create('/admin');
        
        $response = $this->middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertGuest();
    }

    public function test_middleware_handles_empty_session_token(): void
    {
        $user = User::factory()->create();
        $user->setDeviceToken('valid-agent', '127.0.0.1');
        Auth::login($user);
        
        Session::put('device_token', ''); // Empty string
        
        $request = Request::create('/admin');
        
        $response = $this->middleware->handle($request, function () {
            return new Response('OK');
        });

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertGuest();
    }
}
