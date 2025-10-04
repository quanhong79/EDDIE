<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder {
    public function run(): void {
        Product::firstOrCreate([
            'name' => 'Áo thun trắng',
            'price' => 120000,
            'quantity' => 10,
            'category_id' => 2, // giả sử id 2 là áo thun
            'description' => 'Áo thun trắng basic, cotton 100%',
        ]);

        Product::firstOrCreate([
            'name' => 'Quần jean xanh',
            'price' => 250000,
            'quantity' => 5,
            'category_id' => 1, // giả sử id 1 là quần jean
            'description' => 'Quần jean xanh đậm, co giãn',
        ]);
    }
}
