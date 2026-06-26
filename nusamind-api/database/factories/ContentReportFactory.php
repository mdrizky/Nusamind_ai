<?php

namespace Database\Factories;

use App\Models\ContentGeneration;
use App\Models\ContentReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContentReportFactory extends Factory
{
    protected $model = ContentReport::class;

    public function definition(): array
    {
        return [
            'content_generation_id' => ContentGeneration::factory(),
            'reported_by' => User::factory(),
            'reason' => fake()->sentence(),
            'status' => 'pending',
        ];
    }
}
