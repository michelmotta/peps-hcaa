<?php

namespace Tests\Feature\Models;

use App\Models\File;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Imagick;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FileModelTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function o_metodo_uploadSingleFile_salva_um_arquivo_e_cria_o_registro_no_banco(): void
    {
        Storage::fake('public');
        $fakeImage = UploadedFile::fake()->image('imagem.jpg');

        $fileModel = File::uploadSingleFile($fakeImage);

        $this->assertInstanceOf(File::class, $fileModel);
        Storage::disk('public')->assertExists($fileModel->path);
        $this->assertDatabaseHas('files', [
            'id' => $fileModel->id,
            'name' => 'imagem.jpg',
        ]);
    }

    #[Test]
    public function o_metodo_uploadSingleFile_gera_thumbnail_para_um_pdf_real(): void
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('A extensão Imagick não está instalada.');
        }

        Storage::fake('public');

        $pdfContent = "%PDF-1.0\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj 3 0 obj<</Type/Page/MediaBox[0 0 3 3]>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000010 00000 n\n0000000053 00000 n\n0000000102 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n149\n%%EOF";
        $pdfFile = UploadedFile::fake()->createWithContent('documento.pdf', $pdfContent);

        $fileModel = File::uploadSingleFile($pdfFile, null, 'uploads/files', true);

        $this->assertNotNull($fileModel->thumbnail_path);
        $this->assertStringEndsWith('.png', $fileModel->thumbnail_path);
        Storage::disk('public')->assertExists($fileModel->thumbnail_path);
    }

    #[Test]
    public function o_metodo_uploadSingleFile_lida_com_pdf_corrompido_e_retorna_thumbnail_nula(): void
    {
        if (!extension_loaded('imagick')) {
            $this->markTestSkipped('A extensão Imagick não está instalada.');
        }

        Storage::fake('public');
        $corruptedPdf = UploadedFile::fake()->createWithContent('broken.pdf', 'isto-nao-e-um-pdf');

        $model = File::uploadSingleFile($corruptedPdf, null, 'uploads/files', true);

        $this->assertNull($model->thumbnail_path);
        $this->assertDatabaseHas('files', ['id' => $model->id]);
    }

    #[Test]
    public function o_metodo_uploadSingleFile_lanca_excecao_e_apaga_o_arquivo_se_o_banco_falhar(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Não foi possível salvar o arquivo: Erro de banco de dados forçado');

        Storage::fake('public');
        $fakeFile = UploadedFile::fake()->create('arquivo.txt');

        File::saving(function () {
            throw new Exception('Erro de banco de dados forçado');
        });

        try {
            File::uploadSingleFile($fakeFile);
        } finally {

            $path = 'uploads/files/' . $fakeFile->hashName();
            Storage::disk('public')->assertMissing($path);
        }
    }

    #[Test]
    public function o_acessor_getSizeInMbAttribute_formata_o_tamanho_corretamente(): void
    {
        $file = File::factory()->make(['size' => 1572864]); // 1.5 MB
        $this->assertEquals('1.50 MB', $file->size_in_mb);
    }
}
