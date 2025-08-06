<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Topic>
 */
class TopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'lesson_id' => Lesson::factory(),
            'video_id' => Video::factory(),
            'resume' => $this->faker->paragraph(2),
            'description' => $this->faker->paragraph(5),
            'attachments' => null,
        ];
    }
}
