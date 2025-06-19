<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profiles = [
            [
                'name' => 'Coordenador',
                'description' => 'Perfil responsável pela coordenação.'
            ],
            [
                'name' => 'Professor',
                'description' => 'Perfil responsável por ministrar aulas e avaliar estudantes.'
            ],
        ];

        foreach ($profiles as $profile) {
            Profile::factory()->create($profile);
        }
    }
}
