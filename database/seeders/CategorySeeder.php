<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik dan aksesoris'],
            ['name' => 'Alat Tulis Kantor', 'description' => 'Perlengkapan kantor dan ATK'],
            ['name' => 'Furniture', 'description' => 'Mebel dan perabotan kantor'],
            ['name' => 'Kebersihan', 'description' => 'Peralatan dan bahan kebersihan'],
            ['name' => 'Lainnya', 'description' => 'Barang lain-lain'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}