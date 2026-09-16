<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class GoogleSocialiteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Google auth redirect.
     */
    public function test_google_auth_redirects_correctly()
    {
        $response = $this->get(route('auth.google'));

        // It should redirect to google.com domain
        $response->assertRedirect();
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    /**
     * Test Google auth callback logins user and redirects.
     */
    public function test_google_auth_callback_logins_user()
    {
        $mockUser = $this->createMock(\Laravel\Socialite\Two\User::class);
        $mockUser->method('getEmail')->willReturn('googletestuser@gmail.com');
        $mockUser->method('getName')->willReturn('Google Test User');

        $mockProvider = $this->createMock(\Laravel\Socialite\Two\GoogleProvider::class);
        $mockProvider->method('stateless')->willReturnSelf();
        $mockProvider->method('user')->willReturn($mockUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($mockProvider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertOk();
        $response->assertViewIs('client.auth_callback');
        $response->assertViewHas('targetUrl', route('trangchu'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'googletestuser@gmail.com',
            'role' => 'customer',
        ]);
    }
}

