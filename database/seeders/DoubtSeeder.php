<?php

namespace Database\Seeders;

use App\Models\Doubt;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoubtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doubts = [
            [
                'lesson_id' => 1,
                'user_id' => 4,
                'doubt' => 'Quais outras metodologias podem ser empregadas para prevenir situações adversas?',
                'description' => null,
                'answered' => false,
                'answered_at' => null,
            ],
            [
                'lesson_id' => 2,
                'user_id' => 4,
                'doubt' => 'Quais outras metodologias podem ser empregadas para prevenir situações adversas?',
                'description' => 'Podem ser adotadas as metodologias X, Y e Z.',
                'answered' => true,
                'answered_at' => Carbon::now(),
            ],
        ];

        foreach ($doubts as $doubt) {
            Doubt::create($doubt);
        }
    }
}
