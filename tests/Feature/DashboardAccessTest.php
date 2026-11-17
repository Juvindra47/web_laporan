<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_open_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);

        $this->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_petugas_can_login_and_open_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'petugas@example.test',
            'password' => 'password',
            'role' => User::ROLE_PETUGAS,
            'is_active' => true,
        ]);

        $this->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('petugas.dashboard'));

        $this->actingAs($user)
            ->get(route('petugas.dashboard'))
            ->assertOk();
    }

    public function test_pelapor_can_login_and_open_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'pelapor@example.test',
            'password' => 'password',
            'role' => User::ROLE_PELAPOR,
            'is_active' => true,
        ]);

        $this->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('pelapor.dashboard'));

        $this->actingAs($user)
            ->get(route('pelapor.dashboard'))
            ->assertOk();
    }

    public function test_admin_pages_render_without_errors(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);

        foreach ([
            'admin.dashboard',
            'admin.reports.index',
            'admin.verifications',
            'admin.assignments.index',
            'admin.users.index',
            'admin.users.create',
            'admin.categories.index',
            'admin.categories.create',
            'admin.rooms.index',
            'admin.rooms.create',
            'admin.progress',
            'admin.activities',
            'admin.exports.index',
            'admin.settings.edit',
            'profile.edit',
            'notifications.index',
        ] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }

    public function test_petugas_pages_render_without_errors(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PETUGAS, 'is_active' => true]);

        foreach ([
            'petugas.dashboard',
            'petugas.tasks.index',
            'petugas.tasks.history',
            'profile.edit',
            'notifications.index',
        ] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }

    public function test_pelapor_pages_render_without_errors(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_PELAPOR, 'is_active' => true]);

        foreach ([
            'pelapor.dashboard',
            'pelapor.reports.index',
            'pelapor.reports.create',
            'profile.edit',
            'notifications.index',
        ] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }
}
