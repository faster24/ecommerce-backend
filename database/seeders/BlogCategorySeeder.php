<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Latest news and updates in the tech world',
                'is_visible' => true,
                'seo_title' => 'Technology News and Updates',
                'seo_description' => 'Stay updated with the latest technology trends and innovations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Tips and ideas for better living',
                'is_visible' => true,
                'seo_title' => 'Lifestyle Tips and Ideas',
                'seo_description' => 'Discover ways to improve your daily life and wellbeing',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Travel',
                'slug' => 'travel',
                'description' => 'Explore destinations and travel tips',
                'is_visible' => false,
                'seo_title' => 'Travel Destinations and Tips',
                'seo_description' => 'Find inspiration for your next adventure',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert the categories into the database
        DB::table('blog_categories')->insert($categories);
    }
}
