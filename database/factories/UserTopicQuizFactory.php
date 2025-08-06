<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserTopicQuiz>
 */
class UserTopicQuizFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalCount = 5;
        $correctCount = $this->faker->numberBetween(0, $totalCount);
        $score = ($correctCount / $totalCount) * 100;

        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'topic_id' => Topic::factory(),
            'correct_count' => $correctCount,
            'total_count' => $totalCount,
            'score' => $score,
            'passed' => $score >= 70,
            'attempt_number' => 1,
        ];
    }
}
