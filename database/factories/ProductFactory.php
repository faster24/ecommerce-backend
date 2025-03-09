<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_brand_id' => rand(1 , 10),
            'name' => $this->faker->name,
            'slug' => $this->faker->words,
            'sku' => rand(10, 100),
            'description' => $this->faker->sentence,
            'featured' => rand(0,1),
            'is_visible' => rand(0,1),
            'price' => rand(100 , 1000),
            'type' => 'deliverable',
        ];
    }
}
