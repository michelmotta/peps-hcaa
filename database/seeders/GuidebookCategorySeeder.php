<?php

namespace Database\Seeders;

use App\Models\GuidebookCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuidebookCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Aula', 'icon' => 'book-open'],
            ['name' => 'Sugestões', 'icon' => 'message-circle'],
            ['name' => 'Relatórios', 'icon' => 'bar-chart'],
            ['name' => 'Especialidades', 'icon' => 'activity'],
            ['name' => 'Biblioteca', 'icon' => 'archive'],
            ['name' => 'Usuários', 'icon' => 'users'],
            ['name' => 'Manuais', 'icon' => 'file-text'],
        ];

        foreach ($categories as $category) {
            GuidebookCategory::create($category);
        }
    }
}
