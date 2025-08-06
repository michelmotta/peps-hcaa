<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true) . '.mp4',
            'path' => 'uploads/fake_videos/' . $this->faker->uuid() . '.mp4',
            'duration' => $this->faker->time('00:H:i'),
            'thumbnail_path' => 'uploads/fake_thumbnails/' . $this->faker->uuid() . '.jpg',
            'mime_type' => 'video/mp4',
            'size' => $this->faker->numberBetween(5000000, 100000000), // 5MB to 100MB
            'extension' => 'mp4',
        ];
    }
}
