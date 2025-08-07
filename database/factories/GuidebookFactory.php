<?php

namespace Database\Factories;

use App\Enums\GuidebookEnum;
use App\Models\GuidebookCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Guidebook>
 */
class GuidebookFactory extends Factory
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
            'type' => $this->faker->randomElement(GuidebookEnum::cases()),
            'description' => $this->faker->paragraph(5),
            'guidebook_category_id' => GuidebookCategory::factory(),
        ];
    }
}
