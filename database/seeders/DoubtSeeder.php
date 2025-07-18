<?php

namespace Database\Seeders;

use App\Models\Doubt;
use App\Models\User;
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
        $lessonIds = range(1, 4);
        $userIds = User::pluck('id')->all();

        $doubts = collect();

        foreach ($userIds as $userId) {
            $numDoubts = rand(1, 2);
            $selectedLessons = collect($lessonIds)->shuffle()->take($numDoubts);

            foreach ($selectedLessons as $lessonId) {
                $answered = (bool)rand(0, 1);

                $doubts->push([
                    'lesson_id'    => $lessonId,
                    'user_id'      => $userId,
                    'doubt'        => fake()->sentence(8),
                    'description'  => $answered ? fake()->paragraph() : null,
                    'answered'     => $answered,
                    'answered_at'  => $answered ? Carbon::now()->subDays(rand(1, 10)) : null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }

        Doubt::insert($doubts->toArray());
    }
}
