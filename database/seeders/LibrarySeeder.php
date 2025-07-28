<?php

namespace Database\Seeders;

use App\Models\Library;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = User::inRandomOrder()->first()?->id;

        // Fallback if no user exists
        if (!$userId) {
            $userId = User::factory()->create()->id;
        }

        for ($i = 1; $i <= 8; $i++) {
            Library::create([
                'title'     => 'Documento ' . $i,
                'file_id'   => 14,
                'user_id'   => $userId,
                'created_at' => now()->subDays(rand(0, 30)),
                'updated_at' => now(),
            ]);
        }
    }
}
