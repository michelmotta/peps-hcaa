<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\UpdateTopicRequest;
use App\Models\Lesson;
use App\Models\Topic;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateTopicRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $professor;
    private Lesson $lesson;
    private Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->professor = User::factory()->create();
        $this->lesson = Lesson::factory()->create(['user_id' => $this->professor->id]);
        $this->topic = Topic::factory()->create(['lesson_id' => $this->lesson->id]);

        $this->app['router']->put(
            '/dashboard/lessons/{lesson}/topics/{topic}',
            [\App\Http\Controllers\TopicController::class, 'update']
        )->name('dashboard.lessons.topics.update')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Título Válido do Tópico',
            'resume' => 'Este é um resumo válido.',
            'description' => 'Esta é uma descrição detalhada e válida para o tópico.',
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->professor);
        $video = Video::factory()->create();
        $data = $this->getValidData([
            'video_id' => $video->id,
            'attachments' => null,
            'quiz' => null,
        ]);

        $response = $this->putJson(route('dashboard.lessons.topics.update', [$this->lesson, $this->topic]), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->professor);
        $data = $this->getValidData([$field => $value]);

        $response = $this->putJson(route('dashboard.lessons.topics.update', [$this->lesson, $this->topic]), $data);

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
            'video_id (não é inteiro)' => ['video_id', 'abc'],
            'video_id (não existe)' => ['video_id', 999],
        ];
    }
}
