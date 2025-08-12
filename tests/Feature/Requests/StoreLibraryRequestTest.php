<?php

namespace Tests\Feature\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreLibraryRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->app['router']->post(
            '/dashboard/libraries',
            [\App\Http\Controllers\LibraryController::class, 'store']
        )->name('dashboard.libraries.store')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Título Válido do Item',
            'file' => UploadedFile::fake()->create('documento.pdf', 1000),
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData();

        $response = $this->postJson(route('dashboard.libraries.store'), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->postJson(route('dashboard.libraries.store'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
    }

    public static function validationProvider(): array
    {
        return [
            'title (ausente)' => ['title', ''],
            'title (longo)' => ['title', str_repeat('a', 256)],
            'file (ausente)' => ['file', ''],
            'file (não é arquivo)' => ['file', 'nao-e-arquivo'],
            'file (mimetype inválido)' => ['file', UploadedFile::fake()->image('imagem.jpg')],
            'file (muito grande)' => ['file', UploadedFile::fake()->create('grande.pdf', 3000)], // > 2048 KB
        ];
    }
}
