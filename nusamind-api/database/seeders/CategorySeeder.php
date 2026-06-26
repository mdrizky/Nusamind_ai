<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan & Minuman', 'icon' => 'utensils'],
            ['name' => 'Fashion', 'icon' => 'shirt'],
            ['name' => 'Kerajinan Tangan', 'icon' => 'scissors'],
            ['name' => 'Jasa', 'icon' => 'briefcase'],
            ['name' => 'Pertanian & Sembako', 'icon' => 'leaf'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'name' => $category['name'],
                'icon' => $category['icon'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
