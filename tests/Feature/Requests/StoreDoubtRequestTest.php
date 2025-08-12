<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Controllers\DoubtController;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreDoubtRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->lesson = Lesson::factory()->create();

        $this->app['router']->post(
            '/dashboard/lessons/{lesson}/doubts',
            [DoubtController::class, 'store']
        )->name('dashboard.lessons.doubts.store')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'doubt' => 'Esta é uma dúvida válida.',
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData(['description' => 'Uma descrição opcional.']);

        $response = $this->postJson(route('dashboard.lessons.doubts.store', $this->lesson), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->postJson(route('dashboard.lessons.doubts.store', $this->lesson), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
    }

    public static function validationProvider(): array
    {
        return [
            'doubt (ausente)' => ['doubt', ''],
            'doubt (longo)' => ['doubt', str_repeat('a', 256)],
        ];
    }
}
