<?php

namespace Tests\Feature\Http\Requests;

use App\Models\GuidebookCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateGuidebookCategoryRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private GuidebookCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = GuidebookCategory::factory()->create();

        $this->app['router']->put(
            '/dashboard/guidebook-categories/{guidebook_category}',
            [\App\Http\Controllers\GuidebookCategoryController::class, 'update']
        )->name('dashboard.guidebook-categories.update')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Nome Válido da Categoria',
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData(['icon' => 'book-open']);

        $response = $this->putJson(route('dashboard.guidebook-categories.update', $this->category), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->putJson(route('dashboard.guidebook-categories.update', $this->category), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
    }

    public static function validationProvider(): array
    {
        return [
            'name (ausente)' => ['name', ''],
            'name (longo)' => ['name', str_repeat('a', 256)],
            'icon (longo)' => ['icon', str_repeat('a', 256)],
        ];
    }
}
