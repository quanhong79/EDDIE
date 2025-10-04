<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder {
    public function run(): void {
        $quan = Category::firstOrCreate(['slug' => 'quan'], ['name' => 'Quần']);
        Category::firstOrCreate(['slug' => 'quan-jean'], ['name' => 'Quần jean','parent_id'=>$quan->id]);
        Category::firstOrCreate(['slug' => 'quan-kaki'], ['name' => 'Quần kaki','parent_id'=>$quan->id]);

        $ao = Category::firstOrCreate(['slug' => 'ao'], ['name' => 'Áo']);
        Category::firstOrCreate(['slug' => 'ao-thun'], ['name' => 'Áo thun','parent_id'=>$ao->id]);
        Category::firstOrCreate(['slug' => 'ao-so-mi'], ['name' => 'Áo sơ mi','parent_id'=>$ao->id]);
    }
}
