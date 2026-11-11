<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');
        $locations = Location::all()->keyBy('name');

        $products = [
            [
                'code' => 'ELC-001',
                'name' => 'Laptop ASUS Vivobook 15',
                'category_id' => $categories['Elektronik']->id ?? 1,
                'location_id' => $locations['Gudang Utama']->id ?? 1,
                'unit' => 'Unit',
                'stock' => 10,
                'minimum_stock' => 2,
                'purchase_price' => 7500000,
                'selling_price' => 9000000,
                'description' => 'Laptop untuk keperluan kantor',
                'is_active' => true,
            ],
            [
                'code' => 'ELC-002',
                'name' => 'Mouse Wireless Logitech',
                'category_id' => $categories['Elektronik']->id ?? 1,
                'location_id' => $locations['Gudang Utama']->id ?? 1,
                'unit' => 'Unit',
                'stock' => 50,
                'minimum_stock' => 10,
                'purchase_price' => 150000,
                'selling_price' => 250000,
                'description' => 'Mouse wireless untuk kantor',
                'is_active' => true,
            ],
            [
                'code' => 'ELC-003',
                'name' => 'Keyboard Mechanical',
                'category_id' => $categories['Elektronik']->id ?? 1,
                'location_id' => $locations['Gudang Utama']->id ?? 1,
                'unit' => 'Unit',
                'stock' => 20,
                'minimum_stock' => 5,
                'purchase_price' => 450000,
                'selling_price' => 650000,
                'description' => 'Keyboard mechanical untuk developer',
                'is_active' => true,
            ],
            [
                'code' => 'ATK-001',
                'name' => 'Kertas A4 70gsm (1 Rim)',
                'category_id' => $categories['Alat Tulis Kantor']->id ?? 2,
                'location_id' => $locations['Gudang Utama']->id ?? 1,
                'unit' => 'Rim',
                'stock' => 100,
                'minimum_stock' => 20,
                'purchase_price' => 45000,
                'selling_price' => 60000,
                'description' => 'Kertas fotokopi A4 70gsm',
                'is_active' => true,
            ],
            [
                'code' => 'ATK-002',
                'name' => 'Pulpen Ballpoint Hitam (Box 50 pcs)',
                'category_id' => $categories['Alat Tulis Kantor']->id ?? 2,
                'location_id' => $locations['Gudang Utama']->id ?? 1,
                'unit' => 'Box',
                'stock' => 30,
                'minimum_stock' => 5,
                'purchase_price' => 75000,
                'selling_price' => 100000,
                'description' => 'Pulpen hitam standard kantor',
                'is_active' => true,
            ],
            [
                'code' => 'ATK-003',
                'name' => 'Stapler Ukuran Kecil',
                'category_id' => $categories['Alat Tulis Kantor']->id ?? 2,
                'location_id' => $locations['Gudang Cadangan']->id ?? 2,
                'unit' => 'Unit',
                'stock' => 25,
                'minimum_stock' => 5,
                'purchase_price' => 35000,
                'selling_price' => 50000,
                'description' => 'Stapler kecil untuk dokumen',
                'is_active' => true,
            ],
            [
                'code' => 'FUR-001',
                'name' => 'Meja Kerja Ergonomis',
                'category_id' => $categories['Furniture']->id ?? 3,
                'location_id' => $locations['Gudang Utama']->id ?? 1,
                'unit' => 'Unit',
                'stock' => 15,
                'minimum_stock' => 3,
                'purchase_price' => 1200000,
                'selling_price' => 1800000,
                'description' => 'Meja kerja tinggi dapat disesuaikan',
                'is_active' => true,
            ],
            [
                'code' => 'FUR-002',
                'name' => 'Kursi Kantor Mesh',
                'category_id' => $categories['Furniture']->id ?? 3,
                'location_id' => $locations['Gudang Utama']->id ?? 1,
                'unit' => 'Unit',
                'stock' => 20,
                'minimum_stock' => 5,
                'purchase_price' => 850000,
                'selling_price' => 1200000,
                'description' => 'Kursi ergonomis dengan mesh back',
                'is_active' => true,
            ],
            [
                'code' => 'CLN-001',
                'name' => 'Tisu Roll (Pack 12 Roll)',
                'category_id' => $categories['Kebersihan']->id ?? 4,
                'location_id' => $locations['Gudang Cadangan']->id ?? 2,
                'unit' => 'Pack',
                'stock' => 40,
                'minimum_stock' => 10,
                'purchase_price' => 65000,
                'selling_price' => 90000,
                'description' => 'Tisu toilet roll 2 ply',
                'is_active' => true,
            ],
            [
                'code' => 'CLN-002',
                'name' => 'Cairan Pembersih Lantai 1L',
                'category_id' => $categories['Kebersihan']->id ?? 4,
                'location_id' => $locations['Gudang Cadangan']->id ?? 2,
                'unit' => 'Botol',
                'stock' => 60,
                'minimum_stock' => 15,
                'purchase_price' => 25000,
                'selling_price' => 40000,
                'description' => 'Pembersih lantai anti kuman',
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}