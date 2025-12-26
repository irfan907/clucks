<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $freshProduce = Category::where('name', 'Fresh Produce')->first();
        $dairyProducts = Category::where('name', 'Dairy Products')->first();

        if ($freshProduce) {
            Product::create([
                'category_id' => $freshProduce->id,
                'name' => 'Tomatoes',
                'unit' => 'kg',
                'minimum_quantity' => 10.00,
                'default_ordered_quantity' => 20.00,
                'display_order' => 1,
                'is_active' => true,
            ]);

            Product::create([
                'category_id' => $freshProduce->id,
                'name' => 'Lettuce',
                'unit' => 'kg',
                'minimum_quantity' => 5.00,
                'default_ordered_quantity' => 15.00,
                'display_order' => 2,
                'is_active' => true,
            ]);

            Product::create([
                'category_id' => $freshProduce->id,
                'name' => 'Carrots',
                'unit' => 'kg',
                'minimum_quantity' => 8.00,
                'default_ordered_quantity' => 25.00,
                'display_order' => 3,
                'is_active' => true,
            ]);

            Product::create([
                'category_id' => $freshProduce->id,
                'name' => 'Onions',
                'unit' => 'kg',
                'minimum_quantity' => 12.00,
                'default_ordered_quantity' => 30.00,
                'display_order' => 4,
                'is_active' => true,
            ]);
        }

        if ($dairyProducts) {
            Product::create([
                'category_id' => $dairyProducts->id,
                'name' => 'Whole Milk',
                'unit' => 'liters',
                'minimum_quantity' => 20.00,
                'default_ordered_quantity' => 50.00,
                'display_order' => 1,
                'is_active' => true,
            ]);

            Product::create([
                'category_id' => $dairyProducts->id,
                'name' => 'Cheddar Cheese',
                'unit' => 'kg',
                'minimum_quantity' => 5.00,
                'default_ordered_quantity' => 15.00,
                'display_order' => 2,
                'is_active' => true,
            ]);

            Product::create([
                'category_id' => $dairyProducts->id,
                'name' => 'Butter',
                'unit' => 'kg',
                'minimum_quantity' => 3.00,
                'default_ordered_quantity' => 10.00,
                'display_order' => 3,
                'is_active' => true,
            ]);
        }
    }
}

