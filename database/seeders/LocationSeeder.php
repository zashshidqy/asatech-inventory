<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Gudang Utama', 'description' => 'Gudang penyimpanan utama'],
            ['name' => 'Gudang Cadangan', 'description' => 'Gudang penyimpanan cadangan'],
            ['name' => 'Ruang Server', 'description' => 'Ruang server dan perangkat jaringan'],
            ['name' => 'Ruang Rapat', 'description' => 'Ruang rapat dan meeting'],
            ['name' => 'Lokasi Lainnya', 'description' => 'Lokasi penyimpanan lainnya'],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}