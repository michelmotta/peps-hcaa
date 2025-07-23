<?php

namespace Database\Seeders;

use App\Models\LessonUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LessonUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessonUsers = collect();
        $userIds = range(4, 24);
        $availableLessons = range(1, 4);

        foreach ($userIds as $userId) {
            $assignedLessons = collect($availableLessons)
                ->shuffle()
                ->take(rand(1, 4));

            foreach ($assignedLessons as $lessonId) {
                $isFinished = (bool)rand(0, 1);
                $createdAt = now();

                $lessonUsers->push([
                    'lesson_id'   => $lessonId,
                    'user_id'     => $userId,
                    'score'       => $isFinished ? rand(60, 100) : null,
                    'finished'    => $isFinished,
                    'finished_at' => $isFinished ? $createdAt->copy()->addMinutes(rand(1, 120)) : null,
                    'created_at'  => $createdAt,
                    'updated_at'  => $createdAt,
                ]);
            }
        }

        LessonUser::insert($lessonUsers->toArray());
    }
}
