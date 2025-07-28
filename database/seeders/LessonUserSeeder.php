<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\LessonUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LessonUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessonUsers = collect();
        $userIds = User::pluck('id')->toArray();
        $lessonIds = Lesson::pluck('id')->toArray();

        foreach ($lessonIds as $lessonId) {
            foreach ($userIds as $userId) {
                $isFinished = fake()->boolean();
                $createdAt = now();

                $lessonUsers->push([
                    'lesson_id'   => $lessonId,
                    'user_id'     => $userId,
                    'score'       => $isFinished ? rand(60, 100) : null,
                    'finished'    => $isFinished,
                    'finished_at' => $isFinished ? $createdAt->addMinutes(rand(100, 500)) : null,
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt,
                ]);
            }
        }

        LessonUser::insert($lessonUsers->toArray());
    }
}
