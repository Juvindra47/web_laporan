<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Password awal HANYA untuk pengembangan. Ganti segera lewat menu Profil
     * atau ubah konstanta ini sebelum menjalankan seeder.
     */
    private const DEFAULT_PASSWORD = 'opencode';

    public function run(): void
    {
        $users = [
            ['name' => 'Administrator', 'email' => 'admin@simaksek.test', 'username' => 'admin', 'role' => User::ROLE_ADMIN],
            ['name' => 'Petugas Sarpras', 'email' => 'petugas@simaksek.test', 'username' => 'petugas', 'role' => User::ROLE_PETUGAS],
            ['name' => 'Siswa Contoh', 'email' => 'pelapor@simaksek.test', 'username' => 'pelapor', 'role' => User::ROLE_PELAPOR],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                $data + ['password' => self::DEFAULT_PASSWORD, 'is_active' => true]
            );
        }
    }
}
