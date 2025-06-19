<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ProfileSeeder::class);
        $this->call(FileSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ProfileUserSeeder::class);
        $this->call(InformationSeeder::class);
        $this->call(SpecialtySeeder::class);
        $this->call(SuggestionSeeder::class);
        $this->call(LessonSeeder::class);
        $this->call(VideoSeeder::class);
        $this->call(TopicSeeder::class);
        $this->call(QuizSeeder::class);
        $this->call(LessonUserSeeder::class);
        $this->call(DoubtSeeder::class);
    }
}
