<?php

namespace Tests\Feature\Http\Requests;

use App\Enums\GuidebookEnum;
use App\Models\Guidebook;
use App\Models\GuidebookCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateGuidebookRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Guidebook $guidebook;
    private GuidebookCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = GuidebookCategory::factory()->create();
        $this->guidebook = Guidebook::factory()->create(['guidebook_category_id' => $this->category->id]);

        $this->app['router']->put(
            '/dashboard/guidebooks/{guidebook}',
            [\App\Http\Controllers\GuidebookController::class, 'update']
        )->name('dashboard.guidebooks.update')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Título Válido do Guia',
            'type' => GuidebookEnum::INTERN->value,
            'guidebook_category_id' => $this->category->id,
            'description' => 'Esta é uma descrição válida.',
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData();

        $response = $this->putJson(route('dashboard.guidebooks.update', $this->guidebook), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->putJson(route('dashboard.guidebooks.update', $this->guidebook), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
    }

    public static function validationProvider(): array
    {
        return [
            'title (ausente)' => ['title', ''],
            'title (longo)' => ['title', str_repeat('a', 256)],
            'type (ausente)' => ['type', ''],
            'guidebook_category_id (ausente)' => ['guidebook_category_id', ''],
            'description (ausente)' => ['description', ''],
        ];
    }
}
