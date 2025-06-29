<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Especialidades principais (com file_id)
        $principais = [
            'Cardiologia' => 1,
            'Pediatria' => 2,
            'Ortopedia' => 3,
            'Dermatologia' => 4,
            'Ginecologia' => 5,
            'Neurologia' => 6,
            'Psiquiatria' => 7,
            'Oftalmologia' => 8,
        ];

        $map = [];

        foreach ($principais as $nome => $fileId) {
            $especialidade = Specialty::create([
                'name' => $nome,
                'file_id' => $fileId,
                'parent_id' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $map[$nome] = $especialidade->id;
        }

        // 2. Subespecialidades (sem file_id)
        $subs = [
            'Cardiologia' => [
                'Cardiologia Intervencionista',
                'Eletrofisiologia Cardíaca',
            ],
            'Pediatria' => [
                'Neonatologia',
                'Pediatria do Desenvolvimento',
            ],
            'Ortopedia' => [
                'Ortopedia Pediátrica',
                'Cirurgia da Coluna',
            ],
            'Dermatologia' => [
                'Dermatologia Estética',
                'Dermatologia Oncológica',
            ],
            'Ginecologia' => [
                'Ginecologia Endócrina',
                'Ginecologia Oncológica',
            ],
            'Neurologia' => [
                'Neurologia Vascular',
                'Epileptologia',
            ],
            'Psiquiatria' => [
                'Psiquiatria da Infância',
                'Psiquiatria Geriátrica',
            ],
            'Oftalmologia' => [
                'Retina e Vítreo',
                'Córnea e Doenças Externas',
            ],
        ];

        foreach ($subs as $pai => $subNomes) {
            foreach ($subNomes as $sub) {
                Specialty::create([
                    'name' => $sub,
                    'file_id' => null, // subespecialidades sem imagem
                    'parent_id' => $map[$pai],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
