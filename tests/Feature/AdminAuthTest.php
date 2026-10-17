<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ThrottleRequests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_requires_guest(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get(route('admin.login'))->assertRedirect('/');
    }

    public function test_admin_can_login(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $user = User::factory()->admin()->create(['password' => Hash::make('secret123')]);

        $this->post(route('admin.login.submit'), [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_view_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_customer_cannot_access_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_guest_is_redirected_from_admin(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_logout(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
    }
}
