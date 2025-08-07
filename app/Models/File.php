<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Imagick;

class File extends Model
{
    /** @use HasFactory<\Database\Factories\FileFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'path',
        'thumbnail_path',
        'mime_type',
        'size',
        'extension',
        'description',
        'user_id',
    ];

    /**
     * Handle the uploading of a single file.
     *
     * This method stores the file, creates a corresponding file record in the database, 
     * and associates the file with a user if provided.
     *
     * @param \Illuminate\Http\UploadedFile $file The uploaded file instance.
     * @param int|null $userId The ID of the user (optional).
     * @param string $directory The directory where the file should be stored (default 'uploads/files').
     * @param string $disk The storage disk to use (default 'public').
     * @return \App\Models\File The created file instance.
     */
    public static function uploadSingleFile(UploadedFile $file, ?int $userId = null, string $directory = 'uploads/files', bool $generateThumbnail = false, string $disk = 'public'): self
    {
        try {
            $path = $file->store($directory, $disk);
            $thumbnailPath = null;

            if ($generateThumbnail && $file->getClientMimeType() === 'application/pdf') {
                $thumbnailPath = self::createPdfThumbnail($path, $disk);
            }

            return self::create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'thumbnail_path' => $thumbnailPath,
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'extension' => $file->extension(),
                'user_id' => $userId,
            ]);
        } catch (Exception $e) {
            if (isset($path) && Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
            throw new Exception("Não foi possível salvar o arquivo: " . $e->getMessage());
        }
    }

    private static function createPdfThumbnail(string $pdfPath, string $disk): ?string
    {
        try {
            $imagick = new Imagick();
            $imagick->readImage(Storage::disk($disk)->path($pdfPath) . '[0]');
            $imagick->setImageFormat('png');
            
            $thumbnailFilename = pathinfo($pdfPath, PATHINFO_FILENAME) . '.png';
            $thumbnailDirectory = 'uploads/library/thumbnails';
            $fullThumbnailPath = $thumbnailDirectory . '/' . $thumbnailFilename;

            Storage::disk($disk)->put($fullThumbnailPath, $imagick->getImageBlob());

            $imagick->clear();
            $imagick->destroy();

            return $fullThumbnailPath;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Retorna o tamanho do arquivo em megabytes com duas casas decimais.
     *
     * @return string
     */
    public function getSizeInMbAttribute(): string
    {
        return number_format($this->size / 1048576, 2) . ' MB';
    }
}
