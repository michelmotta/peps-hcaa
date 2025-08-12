<?php

namespace Tests\Feature\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreSuggestionRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /**
     * Define uma rota e dados base para os testes.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        // Usa a rota real para garantir que a cobertura de código seja registrada
        $this->app['router']->post(
            '/dashboard/suggestions',
            [\App\Http\Controllers\SuggestionController::class, 'store']
        )->name('dashboard.suggestions.store')->middleware('web', 'auth');
    }

    /**
     * Retorna os dados base válidos para a requisição.
     */
    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Título Válido da Sugestão',
            'votes' => 10,
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData(['description' => 'Uma descrição opcional.']);

        $response = $this->postJson(route('dashboard.suggestions.store'), $data);

        // Se a validação passar, o controller retornará um redirect (302)
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    /**
     * @dataProvider validationProvider
     */
    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->postJson(route('dashboard.suggestions.store'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
    }

    /**
     * Provedor de dados para os testes de validação.
     */
    public static function validationProvider(): array
    {
        return [
            'name (ausente)' => ['name', ''],
            'name (longo)' => ['name', str_repeat('a', 256)],
            'votes (ausente)' => ['votes', ''],
            'votes (não é numérico)' => ['votes', 'abc'],
        ];
    }
}
