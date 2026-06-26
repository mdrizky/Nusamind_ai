<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->word(),
            'price' => fake()->numberBetween(1000, 100000),
            'stock' => fake()->optional()->numberBetween(0, 100),
            'image_path' => null,
            'description' => fake()->optional()->sentence(),
        ];
    }
}
