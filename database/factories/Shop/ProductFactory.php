<?php

namespace Database\Factories\Shop;

use App\Models\ShopBrand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $watchModels = [
            'Chronomaster Elite',
            'Lunar Voyager',
            'Heritage Diver',
            'AeroSpeed Pro',
            'Celestial Navigator',
            'Eclipse Chronograph',
            'Vanguard Classic',
            'Horizon GMT',
            'Pinnacle Automatic',
            'Regatta Master',
            'Equinox Day-Date',
            'Phantom Aviator',
            'Mariner Explorer',
            'Cosmo Perpetual',
            'Zenith Skeleton',
            'Orion Moonphase',
        ];

        $name = $this->faker->randomElement($watchModels);

        return [
            'shop_brand_id' => $this->faker->numberBetween(1, 3),
            'name' => $name,
            'slug' => fn($attributes) => Str::slug($attributes['name']) . '-' . uniqid(), // Ensure unique slug
            'description' => $this->faker->sentence,
            'featured' => $this->faker->boolean,
            'is_visible' => true,
            'price' => $this->faker->numberBetween(100, 1000),
            'type' => 'deliverable',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
