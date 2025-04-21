<?php

namespace Database\Seeders;

use App\Models\Shop\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sampleFiles = [
            storage_path('sample-uploads/product-1.jpg'),
            storage_path('sample-uploads/product-2.jpg'),
            storage_path('sample-uploads/product-3.jpg'),
            storage_path('sample-uploads/product-4.jpg'),
            storage_path('sample-uploads/product-5.jpg'),
            storage_path('sample-uploads/product-6.jpg'),
            storage_path('sample-uploads/product-7.jpg'),
            storage_path('sample-uploads/product-8.jpg'),
            storage_path('sample-uploads/product-9.jpg'),
        ];

        Product::factory(25)->create()->each(function ($product) use ($sampleFiles) {
            $filePath = fake()->randomElement($sampleFiles);

            logger($filePath);

            if (file_exists($filePath)) {
                $product->addMedia($filePath)
                        ->preservingOriginal()
                        ->toMediaCollection('product-images');
            }
        });
    }
}
