<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'      => $this->faker->word() . '.' . $ext = $this->faker->randomElement(['jpg', 'png', 'gif', 'pdf', 'txt']),
            'path'      => 'uploads/fake/' . $this->faker->uuid() . '.' . $ext,
            'size'      => $this->faker->numberBetween(1024, 102400),
            'extension' => $ext,
            'mime_type' => match ($ext) {
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'pdf' => 'application/pdf',
                'txt' => 'text/plain',
            },
        ];
    }
}
