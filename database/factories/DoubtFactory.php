<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doubt>
 */
class DoubtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doubt' => $this->faker->sentence() . '?',
            'lesson_id' => Lesson::factory(),
            'user_id' => User::factory(),
            'answered' => false,
        ];
    }
}
