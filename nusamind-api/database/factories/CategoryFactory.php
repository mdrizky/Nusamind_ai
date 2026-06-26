<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Makanan & Minuman', 'Fashion', 'Kerajinan Tangan', 'Jasa', 'Pertanian & Sembako',
            ]),
            'icon' => fake()->randomElement(['utensils', 'shirt', 'scissors', 'briefcase', 'leaf']),
        ];
    }
}
