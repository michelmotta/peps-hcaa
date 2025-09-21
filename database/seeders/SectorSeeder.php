<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = [
            ['name' => 'Hospital de Câncer Alfredo Abrão'],
            ['name' => 'Outro']
        ];

        foreach ($setores as $setor) {
            Sector::create($setor);
        }
    }
}
