<?php

namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

class WebsiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Alamat, koordinat, dan Google Maps sengaja DIKOSONGKAN.
        // Admin mengisi data sebenarnya lewat Dashboard > Pengaturan Website.
        if (WebsiteSetting::query()->exists()) {
            return;
        }

        WebsiteSetting::create([
            'school_name' => "SMK Ma'arif NU 1 Ajibarang",
            'hero_type' => 'photo',
            'hero_title' => 'Fasilitas Sekolah Terawat, Lingkungan Belajar Nyaman.',
            'hero_description' => 'SIMAK-SARPRAS memudahkan siswa, guru, dan warga sekolah melaporkan kerusakan fasilitas, lalu memantau proses verifikasi hingga perbaikannya secara transparan.',
            'footer_text' => 'Sistem Informasi Manajemen Kerusakan Sekolah.',
        ]);
    }
}
