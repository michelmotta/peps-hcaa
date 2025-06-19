<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades = [
            [
                'name' => 'Cardiologia',
                'description' => 'Especialidade relacionada ao diagnóstico e tratamento de doenças do coração e vasos sanguíneos.',
                'file_id' => 1,
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Pediatria',
                'description' => 'Especialidade dedicada à saúde de bebês, crianças e adolescentes.',
                'file_id' => 2,
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Ortopedia',
                'description' => 'Especialidade focada no sistema musculoesquelético: ossos, articulações, ligamentos e músculos.',
                'file_id' => 3,
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Dermatologia',
                'description' => 'Especialidade médica que cuida da pele, cabelos, unhas e doenças relacionadas.',
                'file_id' => 4,
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Ginecologia',
                'description' => 'Especialidade que cuida da saúde do sistema reprodutor feminino.',
                'file_id' => 5,
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Neurologia',
                'description' => 'Especialidade médica que estuda e trata doenças do sistema nervoso central e periférico.',
                'file_id' => 6,
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Psiquiatria',
                'description' => 'Especialidade médica dedicada ao diagnóstico, prevenção e tratamento de transtornos mentais.',
                'file_id' => 7,
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Oftalmologia',
                'description' => 'Especialidade focada no tratamento de doenças e cirurgias dos olhos.',
                'file_id' => 8,
                'user_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Inserir as especialidades na base de dados
        foreach ($especialidades as $especialidade) {
            Specialty::create($especialidade);
        }
    }
}
