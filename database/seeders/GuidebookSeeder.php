<?php

namespace Database\Seeders;

use App\Enums\GuidebookEnum;
use App\Models\Guidebook;
use App\Models\GuidebookCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuidebookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = GuidebookCategory::all();

        foreach ($categories as $category) {
            Guidebook::create([
                'title' => 'Guia Interno - ' . $category->name,
                'type' => GuidebookEnum::INTERN->value,
                'description' => 'Este é um guia de uso interno para a categoria ' . $category->name . '.',
                'guidebook_category_id' => $category->id,
            ]);

            Guidebook::create([
                'title' => 'Guia Externo - ' . $category->name,
                'type' => GuidebookEnum::EXTERN->value,
                'description' => 'Este é um guia de uso externo para a categoria ' . $category->name . '.',
                'guidebook_category_id' => $category->id,
            ]);
        }
    }
}
