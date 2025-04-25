<?php

namespace Database\Factories\Shop;

use App\Models\ShopBrand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'number' => 'ORD-' . Str::upper(Str::random(8)),
            'status' => fake()->randomElement(['new', 'processing', 'shipped', 'delivered', 'cancelled']),
            'shipping_price' => fake()->numberBetween(5, 20),
            'created_at' => fake()->dateTimeBetween('-5 year', 'now'),
        ];
    }

}
