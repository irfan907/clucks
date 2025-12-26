<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name' => 'Fresh Produce',
            'description' => 'Fresh fruits and vegetables',
            'display_order' => 1,
            'colour' => '#10B981',
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Dairy Products',
            'description' => 'Milk, cheese, and other dairy items',
            'display_order' => 2,
            'colour' => '#3B82F6',
            'is_active' => true,
        ]);
    }
}

