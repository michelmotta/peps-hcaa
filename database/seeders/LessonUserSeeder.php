<?php

namespace Database\Seeders;

use App\Models\LessonUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LessonUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessonUsers = [
            [
                'lesson_id' => 1,
                'user_id' => 4,
                'score' => null,
                'finished' => false,
                'finished_at' => null,
            ],
            [
                'lesson_id' => 2,
                'user_id' => 4,
                'score' => null,
                'finished' => false,
                'finished_at' => null,
            ]
        ];

        foreach ($lessonUsers as $lessonUser) {
            LessonUser::create($lessonUser);
        }
    }
}
