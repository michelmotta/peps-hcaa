<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VideoUploadTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_metodo_uploadSingleVideo_salva_o_video_gera_thumbnail_e_cria_registro_no_banco(): void
    {
        Storage::fake('public');
        $fakeVideoFile = UploadedFile::fake()->create('test_video.mp4', 1024, 'video/mp4');

        $ffmpegMock = Mockery::mock('alias:' . FFMpeg::class);
        $ffprobeMock = Mockery::mock('alias:' . FFProbe::class);

        $ffmpegMock->shouldReceive('create')->andReturn($ffmpegMock);
        $ffprobeMock->shouldReceive('create')->andReturn($ffprobeMock);

        $ffmpegMock->shouldReceive('open')->andReturnSelf();
        $ffmpegMock->shouldReceive('frame')->andReturnSelf();
        $ffmpegMock->shouldReceive('save')->once();

        $ffprobeMock->shouldReceive('format')->andReturnSelf();
        $ffprobeMock->shouldReceive('get')->with('duration')->andReturn('120.5');

        $videoModel = Video::uploadSingleVideo($fakeVideoFile);

        $this->assertInstanceOf(Video::class, $videoModel);

        Storage::disk('public')->assertExists($videoModel->path);

        $this->assertDatabaseHas('videos', [
            'id' => $videoModel->id,
            'name' => 'test_video.mp4',
            'path' => $videoModel->path,
            'thumbnail_path' => $videoModel->thumbnail_path,
            'duration' => '00:02:00',
            'size' => 1048576,
            'extension' => 'mp4',
        ]);
    }

    #[Test]
    public function lanca_excecao_quando_ffmpeg_falha(): void
    {
        $fakeVideoFile = UploadedFile::fake()->create('fail.mp4', 100, 'video/mp4');
        Storage::fake('public');

        Mockery::mock('alias:' . FFMpeg::class)
            ->shouldReceive('create')
            ->andThrow(new \Exception('Falha no processamento'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Erro ao salvar vídeo: Falha no processamento');

        Video::uploadSingleVideo($fakeVideoFile);
    }
}
