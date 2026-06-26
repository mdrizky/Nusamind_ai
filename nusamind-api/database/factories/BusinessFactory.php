<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'business_name' => fake()->company(),
            'category_id' => Category::factory(),
            'city' => fake()->city(),
            'description' => fake()->sentence(),
        ];
    }
}
