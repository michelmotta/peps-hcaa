<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Controllers\SuggestionController;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateSuggestionRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Suggestion $suggestion;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->suggestion = Suggestion::factory()->create();

        $this->app['router']->patch(
            '/dashboard/suggestions/{suggestion}',
            [SuggestionController::class, 'update']
        )->name('dashboard.suggestions.update')->middleware('web', 'auth');
    }

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

        $response = $this->patchJson(route('dashboard.suggestions.update', $this->suggestion), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->patchJson(route('dashboard.suggestions.update', $this->suggestion), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
    }

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
