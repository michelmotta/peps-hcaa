<?php

namespace Database\Seeders;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $files = [
            [
                'name' => 'icon1.png',
                'path' => 'uploads/specialties/eXud1cb04bUyqYCxiaTLBjxjd1ezEAQJiNSAeXV9.png',
                'mime_type' => 'image/png',
                'size' => 33349,
                'user_id' => null,
                'extension' => 'png',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'icon2.png',
                'path' => 'uploads/specialties/APBzfFGKayN8Vh6rYD2zXjHCLA4VuBQjUlMrH7ES.png',
                'mime_type' => 'image/png',
                'size' => 31821,
                'user_id' => null,
                'extension' => 'png',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'icon3.png',
                'path' => 'uploads/specialties/maFr5hjTB62hVHJBypjgJ2JcWqHDj8POnuK06Lgz.png',
                'mime_type' => 'image/png',
                'size' => 19525,
                'user_id' => null,
                'extension' => 'png',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'icon4.png',
                'path' => 'uploads/specialties/8f5SbTUZ7SG8sqfDovQiqO5gKVmsjAbYfowmrdmh.png',
                'mime_type' => 'image/png',
                'size' => 35593,
                'user_id' => null,
                'extension' => 'png',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'icon5.png',
                'path' => 'uploads/specialties/6tkawuc9kTkwrWRDP4YmG3u3hg50qwZIlAazF57R.png',
                'mime_type' => 'image/png',
                'size' => 34152,
                'user_id' => null,
                'extension' => 'png',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'icon6.png',
                'path' => 'uploads/specialties/G2FMUOygmohMknf3gUwseWSSt3gcoelaH43m5qHz.png',
                'mime_type' => 'image/png',
                'size' => 25849,
                'user_id' => null,
                'extension' => 'png',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'icon7.png',
                'path' => 'uploads/specialties/gAIb39dLuQL81fnTmasJKUxIV0XPi4utydK3Hvlg.png',
                'mime_type' => 'image/png',
                'size' => 40013,
                'user_id' => null,
                'extension' => 'png',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'icon8.png',
                'path' => 'uploads/specialties/dJC2RL651i4cJZ2es5NmCgsgsVdvYAvqooNgxzUT.png',
                'mime_type' => 'image/png',
                'size' => 29083,
                'user_id' => null,
                'extension' => 'png',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'user.png',
                'path' => 'uploads/users/9Bz1uJcnEnoE5ZqfArH5k8CFVLPIgJX1sxY4GCbW.png',
                'mime_type' => 'image/png',
                'size' => 39362,
                'user_id' => null,
                'extension' => 'png',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'lesson1.jpeg',
                'path' => 'uploads/lessons/haidUSBl6HbFHJVTyrX8dTPT65ECyV4lnnXQe4Bd.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 164288,
                'user_id' => null,
                'extension' => 'jpg',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'lesson2.jpg',
                'path' => 'uploads/lessons/6UhJbNVCLBXPz05b5rxX0Y0M2daaRalYMcfHSqS4.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 378863,
                'user_id' => null,
                'extension' => 'jpg',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'lesson3.jpeg',
                'path' => 'uploads/lessons/02HFL6RYFgLIcrOkiHL5gQ1UxjDhCx5Px92aJQPa.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 105092,
                'user_id' => null,
                'extension' => 'jpg',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'lesson4.jpg',
                'path' => 'uploads/lessons/2FMPelTovgBxjfuAMMtPoNvYddYzZJaydD82Zfv8.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 103646,
                'user_id' => null,
                'extension' => 'jpg',
                'description' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        // Inserting the files into the database
        foreach ($files as $file) {
            File::create($file);
        }
    }
}
