<?php

namespace Database\Seeders;

use App\Models\Shop\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Rolex',
                'slug' => 'rolex',
                'website' => 'https://www.rolex.com',
                'description' => 'Swiss luxury watch manufacturer',
                'position' => 1,
                'is_visible' => true,
                'seo_title' => 'Rolex Watches',
                'seo_description' => 'Discover Rolex luxury timepieces',
                'sort' => 10,
            ],
            [
                'name' => 'Omega',
                'slug' => 'omega',
                'website' => 'https://www.omegawatches.com',
                'description' => 'Premium Swiss watch maker',
                'position' => 2,
                'is_visible' => true,
                'seo_title' => 'Omega Timepieces',
                'seo_description' => 'Explore Omega precision watches',
                'sort' => 20,
            ],
            [
                'name' => 'Tag Heuer',
                'slug' => 'tag-heuer',
                'website' => 'https://www.tagheuer.com',
                'description' => 'Luxury Swiss watch brand',
                'position' => 3,
                'is_visible' => true,
                'seo_title' => 'Tag Heuer Watches',
                'seo_description' => 'Browse Tag Heuer luxury chronographs',
                'sort' => 30,
            ],
            [
                'name' => 'Patek Philippe',
                'slug' => 'patek-philippe',
                'website' => 'https://www.patek.com',
                'description' => 'High-end Swiss watch manufacturer',
                'position' => 4,
                'is_visible' => true,
                'seo_title' => 'Patek Philippe Collection',
                'seo_description' => 'Discover Patek Philippe luxury watches',
                'sort' => 40,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
