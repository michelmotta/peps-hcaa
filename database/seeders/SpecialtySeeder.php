<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parents = [
            ['name' => 'Cardiologia', 'file_id' => 1],
            ['name' => 'Pediatria', 'file_id' => 2],
            ['name' => 'Ortopedia', 'file_id' => 3],
            ['name' => 'Dermatologia', 'file_id' => 4],
            ['name' => 'Neurologia', 'file_id' => 5],
            ['name' => 'Ginecologia', 'file_id' => 6],
            ['name' => 'Psiquiatria', 'file_id' => 7],
            ['name' => 'Oftalmologia', 'file_id' => 8],
        ];

        $parentMap = [];

        foreach ($parents as $parentData) {
            $parent = Specialty::create([
                'name' => $parentData['name'],
                'file_id' => $parentData['file_id'],
                'parent_id' => null,
            ]);
            $parentMap[$parent->name] = $parent->id;
        }

        $children = [
            'Cardiologia' => [
                'Cardiologia Intervencionista',
                'Eletrofisiologia Cardíaca',
                'Ecocardiografia',
            ],
            'Pediatria' => [
                'Neonatologia',
                'Pediatria do Desenvolvimento',
                'Endocrinologia Pediátrica',
            ],
            'Ortopedia' => [
                'Ortopedia Pediátrica',
                'Cirurgia da Coluna',
                'Traumatologia Esportiva',
            ],
            'Dermatologia' => [
                'Dermatologia Estética',
                'Dermatologia Oncológica',
                'Dermatopatologia',
            ],
            'Neurologia' => [
                'Neurologia Vascular',
                'Epileptologia',
                'Neurofisiologia Clínica',
            ],
        ];

        foreach ($children as $parentName => $childNames) {
            $parentId = $parentMap[$parentName];

            foreach ($childNames as $childName) {
                Specialty::create([
                    'name' => $childName,
                    'file_id' => null,
                    'parent_id' => $parentId,
                ]);
            }
        }
    }
}
