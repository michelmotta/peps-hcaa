<?php

namespace Database\Factories;

use App\Enums\CertificateTypeEnum;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Certificate>
 */
class CertificateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'type' => $this->faker->randomElement(CertificateTypeEnum::cases()),
            'uuid' => $this->faker->uuid(),
            'issued_at' => now(),
        ];
    }
}
