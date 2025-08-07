<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LessonUser>
 */
class LessonUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $finished = $this->faker->boolean();

        return [
            'lesson_id' => Lesson::factory(),
            'user_id' => User::factory(),
            'score' => $finished ? $this->faker->numberBetween(70, 100) : null,
            'finished' => $finished,
            'finished_at' => $finished ? now() : null,
        ];
    }
}
