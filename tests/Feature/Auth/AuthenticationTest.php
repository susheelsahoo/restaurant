<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create();

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user, 'web');
        $this->assertGuest('mobile');
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('web');
        $this->assertGuest('mobile');
    }

    public function test_mobile_login_uses_mobile_guard_only()
    {
        $user = User::factory()->create();

        $response = $this->post('/mobile/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user, 'mobile');
        $this->assertGuest('web');
        $response->assertRedirect('/mobile/dashboard');
    }

    public function test_mobile_user_cannot_open_admin_login()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'mobile')->get('/admin/login');

        $response->assertRedirect('/mobile/dashboard');
    }

    public function test_admin_user_cannot_open_mobile_login()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->get('/mobile/login');

        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
