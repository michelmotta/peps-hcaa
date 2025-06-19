<?php

namespace App\Models;

use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    /** @use HasFactory<\Database\Factories\VideoFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'path',
        'duration',
        'thumbnail_path',
        'mime_type',
        'size',
        'extension',
    ];

    /**
     * Handles the upload of a single video file, generates a thumbnail,
     * extracts duration, and stores the video metadata in the database.
     *
     * @param UploadedFile $file The uploaded video file.
     * @param string $directory The storage directory path (default: 'uploads/videos').
     * @param string $disk The Laravel filesystem disk to use (default: 'public').
     * @return self The saved Video model instance.
     *
     * @throws Exception If the video processing or saving fails.
     */
    public static function uploadSingleVideo(UploadedFile $file, string $directory = 'uploads/lessons/videos', string $disk = 'public'): self
    {
        try {
            // Save the video and define paths
            $videoPath = $file->store($directory, $disk);
            $filename = pathinfo($file->hashName(), PATHINFO_FILENAME);
            $thumbnailPath = $directory . "/" . $filename . ".jpg";

            $diskStorage = Storage::disk($disk);
            $videoFullPath = $diskStorage->path($videoPath);
            $thumbFullPath = $diskStorage->path($thumbnailPath);

            // Generate thumbnail and extract duration
            FFMpeg::create()->open($videoFullPath)
                ->frame(TimeCode::fromSeconds(1))
                ->save($thumbFullPath);

            $duration = gmdate('H:i:s', (int) FFProbe::create()
                ->format($videoFullPath)
                ->get('duration'));

            // Save video record
            return self::create([
                'name'           => $file->getClientOriginalName(),
                'path'           => $videoPath,
                'thumbnail_path' => $thumbnailPath,
                'duration'       => $duration,
                'mime_type'      => $file->getClientMimeType(),
                'size'           => $file->getSize(),
                'extension'      => $file->extension(),
            ]);
        } catch (Exception $e) {
            throw new Exception("Erro ao salvar vídeo: " . $e->getMessage());
        }
    }
}
