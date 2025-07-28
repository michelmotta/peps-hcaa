<?php

namespace Database\Seeders;

use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $videos = [
            [
                'name' => 'Tópico 1.mp4',
                'path' => 'uploads/lessons/videos/Hjg6GDUQgVm7rm5obHdgowN9WXSXD1uebG3EwX6K.mp4',
                'duration' => '00:02:50',
                'thumbnail_path' => 'uploads/lessons/videos/Hjg6GDUQgVm7rm5obHdgowN9WXSXD1uebG3EwX6K.jpg',
                'mime_type' => 'video/mp4',
                'size' => 31491130,
                'extension' => 'mp4',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Tópico 2.webm',
                'path' => 'uploads/lessons/videos/Hjg6GDUQgVm7rm5obHdgowN9WXSXD1uebG3EwX6K.mp4',
                'duration' => '00:00:32',
                'thumbnail_path' => 'uploads/lessons/videos/Hjg6GDUQgVm7rm5obHdgowN9WXSXD1uebG3EwX6K.jpg',
                'mime_type' => 'video/mp4',
                'size' => 2165175,
                'extension' => 'webm',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Tópico 3.mp4',
                'path' => 'uploads/lessons/videos/uiGhYVR0mhGPDN5Kf2SWM8BAvOp2dTGIiCXkFDDl.mp4',
                'duration' => '00:00:30',
                'thumbnail_path' => 'uploads/lessons/videos/uiGhYVR0mhGPDN5Kf2SWM8BAvOp2dTGIiCXkFDDl.jpg',
                'mime_type' => 'video/mp4',
                'size' => 21657943,
                'extension' => 'mp4',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Inserting the files into the database
        foreach ($videos as $video) {
            Video::create($video);
        }
    }
}
