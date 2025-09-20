<?php

namespace Tests\Unit\Models;

use App\Models\Video;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class VideoTest extends TestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_upload_single_video_success()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('example.mp4', 1024, 'video/mp4');

        $frameMock = Mockery::mock('overload:FFMpeg\Media\Frame');
        $frameMock->shouldReceive('save')->once()->andReturnNull();

        $ffmpegOpenMock = Mockery::mock('overload:FFMpeg\Media\Video');
        $ffmpegOpenMock->shouldReceive('frame')->once()->with(Mockery::type(TimeCode::class))->andReturn($frameMock);

        $ffmpegMock = Mockery::mock('overload:' . FFMpeg::class);
        $ffmpegMock->shouldReceive('create')->once()->andReturnSelf();
        $ffmpegMock->shouldReceive('open')->once()->andReturn($ffmpegOpenMock);

        $formatMock = Mockery::mock('overload:FFMpeg\Format\FormatInterface');
        $formatMock->shouldReceive('get')->once()->with('duration')->andReturn(65);

        $ffprobeMock = Mockery::mock('overload:' . FFProbe::class);
        $ffprobeMock->shouldReceive('create')->once()->andReturnSelf();
        $ffprobeMock->shouldReceive('format')->once()->andReturn($formatMock);

        $video = Video::uploadSingleVideo($file, 'uploads/lessons/videos', 'public');

        $this->assertDatabaseHas('videos', [
            'name' => 'example.mp4',
            'path' => $video->path,
            'thumbnail_path' => $video->thumbnail_path,
            'duration' => '00:01:05',
            'mime_type' => 'video/mp4',
            'size' => $file->getSize(),
            'extension' => 'mp4',
        ]);

        Storage::disk('public')->assertExists($video->path);
    }

    public function test_upload_single_video_throws_exception()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('example.mp4', 1024, 'video/mp4');

        $ffmpegMock = Mockery::mock('overload:' . FFMpeg::class);
        $ffmpegMock->shouldReceive('create')->once()->andReturnSelf();
        $ffmpegMock->shouldReceive('open')->andThrow(new Exception('FFMpeg failed'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Erro ao salvar vídeo: FFMpeg failed');

        Video::uploadSingleVideo($file, 'uploads/lessons/videos', 'public');
    }
}
