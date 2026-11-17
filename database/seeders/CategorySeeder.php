<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Kelistrikan' => 'Lampu, stop kontak, kabel, saklar, kipas angin.',
            'Air dan Sanitasi' => 'Kran, pipa, toilet, saluran air.',
            'Mebel dan Perabot' => 'Meja, kursi, lemari, papan tulis.',
            'Bangunan' => 'Atap, dinding, lantai, pintu, jendela, plafon.',
            'Peralatan Praktik' => 'Mesin dan alat bengkel atau laboratorium.',
            'Jaringan dan Perangkat IT' => 'Komputer, proyektor, internet, perangkat jaringan.',
            'Lainnya' => 'Kerusakan fasilitas lain yang tidak termasuk kategori di atas.',
        ];

        foreach ($items as $name => $description) {
            Category::firstOrCreate(['name' => $name], ['description' => $description]);
        }
    }
}
