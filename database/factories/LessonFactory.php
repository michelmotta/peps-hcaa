<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Aula de Teste: ' . fake()->sentence(3),
            'description' => fake()->paragraph(),
            'workload' => 10,
            'user_id' => User::factory(),
            'file_id' => File::factory(),
        ];
    }
}
