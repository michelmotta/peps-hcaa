<?php

namespace Tests\Feature\Http\Requests;

use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateSpecialtyRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Specialty $specialty;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->specialty = Specialty::factory()->create();

        $this->app['router']->put(
            '/dashboard/specialties/{specialty}',
            [\App\Http\Controllers\SpecialtyController::class, 'update']
        )->name('dashboard.specialties.update')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Nome Válido da Especialidade',
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([
            'description' => 'Uma descrição opcional.',
            'file' => UploadedFile::fake()->image('icon.png'),
        ]);

        $response = $this->putJson(route('dashboard.specialties.update', $this->specialty), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->putJson(route('dashboard.specialties.update', $this->specialty), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
    }

    public static function validationProvider(): array
    {
        return [
            'name (ausente)' => ['name', ''],
            'name (longo)' => ['name', str_repeat('a', 256)],
            'file (não é arquivo)' => ['file', 'nao-e-arquivo'],
            'file (mimetype inválido)' => ['file', UploadedFile::fake()->create('document.pdf')],
            'file (muito grande)' => ['file', UploadedFile::fake()->create('grande.jpg', 3000)],
        ];
    }
}
