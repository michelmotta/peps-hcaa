<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Controllers\TopicController;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreTopicRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $professor;
    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->professor = User::factory()->create();
        $this->lesson = Lesson::factory()->create(['user_id' => $this->professor->id]);

        $this->app['router']->post(
            '/dashboard/lessons/{lesson}/topics',
            [TopicController::class, 'store']
        )->name('dashboard.lessons.topics.store')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Título Válido do Tópico',
            'resume' => 'Este é um resumo válido.',
            'description' => 'Esta é uma descrição detalhada e válida para o tópico.',
            'video_id' => Video::factory()->create()->id,
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->professor);
        $data = $this->getValidData([
            'attachments' => null,
            'quiz' => null,
        ]);

        $response = $this->postJson(route('dashboard.lessons.topics.store', $this->lesson), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->professor);
        $data = $this->getValidData([$field => $value]);

        $response = $this->postJson(route('dashboard.lessons.topics.store', $this->lesson), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
    }

    public static function validationProvider(): array
    {
        return [
            'title (ausente)' => ['title', ''],
            'title (longo)' => ['title', str_repeat('a', 256)],
            'resume (ausente)' => ['resume', ''],
            'description (ausente)' => ['description', ''],
            'video_id (ausente)' => ['video_id', ''],
            'video_id (não é inteiro)' => ['video_id', 'abc'],
            'video_id (não existe)' => ['video_id', 999],
        ];
    }
}
