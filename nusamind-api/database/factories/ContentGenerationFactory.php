<?php

namespace Database\Factories;

use App\Models\ContentGeneration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentGenerationFactory extends Factory
{
    protected $model = ContentGeneration::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'image_path' => 'uploads/test.jpg',
            'style' => fake()->randomElement(['gaul', 'formal', 'hard_selling']),
            'caption_result' => fake()->sentence(),
            'hashtags_result' => [fake()->word(), fake()->word()],
            'whatsapp_template_result' => fake()->sentence(),
        ];
    }
}
