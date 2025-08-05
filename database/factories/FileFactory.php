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
            'name' => $this->faker->word() . '.' . $this->faker->fileExtension(),
            'path' => 'uploads/fake/' . $this->faker->uuid() . '.' . $this->faker->fileExtension(),
            'size' => $this->faker->numberBetween(1024, 102400),
            'extension' => $this->faker->fileExtension(),
            'mime_type' => $this->faker->mimeType(),
        ];
    }
}
