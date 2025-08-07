<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Controllers\LessonController;
use App\Models\Lesson;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateLessonRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->lesson = Lesson::factory()->create();
        Specialty::factory()->create(['id' => 1]);

        $this->app['router']->put(
            '/dashboard/lessons/{lesson}',
            [LessonController::class, 'update']
        )->name('dashboard.lessons.update')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Nome Válido da Aula',
            'description' => 'Descrição válida da aula.',
            'workload' => 40,
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([
            'specialty_ids' => [1],
            'file' => UploadedFile::fake()->image('thumbnail.jpg'),
        ]);

        $response = $this->putJson(route('dashboard.lessons.update', $this->lesson), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value, ?string $errorKey = null): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->putJson(route('dashboard.lessons.update', $this->lesson), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($errorKey ?? $field);
    }

    public static function validationProvider(): array
    {
        return [
            'name (ausente)' => ['name', ''],
            'name (longo)' => ['name', str_repeat('a', 256)],
            'description (ausente)' => ['description', ''],
            'workload (ausente)' => ['workload', ''],
            'workload (não é inteiro)' => ['workload', 'abc'],
            'specialty_ids (não é array)' => ['specialty_ids', 'nao-e-array'],
            'specialty_ids (id não existe)' => ['specialty_ids', [999], 'specialty_ids.0'],
            'file (não é arquivo)' => ['file', 'nao-e-arquivo'],
            'file (mimetype inválido)' => ['file', UploadedFile::fake()->create('document.pdf')],
            'file (muito grande)' => ['file', UploadedFile::fake()->create('grande.jpg', 3000)], // > 2048 KB
        ];
    }
}
