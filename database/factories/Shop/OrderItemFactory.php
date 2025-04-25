<?php

namespace Database\Factories\Shop;

use App\Models\ShopBrand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderItemFactory extends Factory
{
    public function definition() : array
    {
        return [
           'qty' => fake()->numberBetween(1, 5),
        ];
    }
}
