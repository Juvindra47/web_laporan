<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /** Data contoh generik. Admin sebaiknya mengganti dengan ruangan asli sekolah lewat menu Data Ruangan. */
    public function run(): void
    {
        $rooms = ['Ruang Kelas', 'Laboratorium', 'Perpustakaan', 'Bengkel Praktik', 'Ruang Guru', 'Toilet', 'Musala', 'Halaman / Lapangan'];

        foreach ($rooms as $name) {
            Room::firstOrCreate(['room_name' => $name], ['description' => 'Data contoh, silakan ubah sesuai kondisi sekolah.']);
        }
    }
}
