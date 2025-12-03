<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Dry Fruit Holders',
                'slug' => 'dry-fruit-holders',
                'description' => 'Beautifully crafted holders for storing and serving dry fruits',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Wall Decor',
                'slug' => 'wall-decor',
                'description' => 'Handcrafted wall art and decorative pieces',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Festive Decor',
                'slug' => 'festive-decor',
                'description' => 'Special decorative items for festivals and celebrations',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Home Accents',
                'slug' => 'home-accents',
                'description' => 'Unique handicraft pieces to enhance your home',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
