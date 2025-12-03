<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Design Styles
            ['name' => 'Warli', 'slug' => 'warli', 'type' => 'design_style'],
            ['name' => 'Rajasthani', 'slug' => 'rajasthani', 'type' => 'design_style'],
            ['name' => 'Traditional', 'slug' => 'traditional', 'type' => 'design_style'],
            ['name' => 'Contemporary', 'slug' => 'contemporary', 'type' => 'design_style'],

            // Materials
            ['name' => 'Wood', 'slug' => 'wood', 'type' => 'material'],
            ['name' => 'Clay', 'slug' => 'clay', 'type' => 'material'],
            ['name' => 'Metal', 'slug' => 'metal', 'type' => 'material'],
            ['name' => 'Fabric', 'slug' => 'fabric', 'type' => 'material'],

            // Occasions
            ['name' => 'Diwali', 'slug' => 'diwali', 'type' => 'occasion'],
            ['name' => 'Wedding', 'slug' => 'wedding', 'type' => 'occasion'],
            ['name' => 'Housewarming', 'slug' => 'housewarming', 'type' => 'occasion'],

            // General
            ['name' => 'Handmade', 'slug' => 'handmade', 'type' => 'general'],
            ['name' => 'Eco-Friendly', 'slug' => 'eco-friendly', 'type' => 'general'],
            ['name' => 'Gift', 'slug' => 'gift', 'type' => 'general'],
        ];

        foreach ($tags as $tag) {
            \App\Models\Tag::create($tag);
        }
    }
}
