<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk()
            ->assertSee('Daftar Akun Pelapor');
    }

    public function test_guest_can_register_as_pelapor(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Siswa Baru',
            'email' => 'siswabaru@sekolah.sch.id',
            'username' => 'siswabaru',
            'phone' => '08123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('pelapor.reports.create'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Siswa Baru',
            'email' => 'siswabaru@sekolah.sch.id',
            'username' => 'siswabaru',
            'role' => User::ROLE_PELAPOR,
            'is_active' => true,
        ]);
    }
}
