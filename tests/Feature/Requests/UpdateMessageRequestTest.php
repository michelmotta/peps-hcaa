<?php

namespace Tests\Feature\Http\Requests;

use App\Models\Lesson;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateMessageRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Lesson $lesson;
    private Message $message;

    /**
     * Define uma rota e dados base para os testes.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->lesson = Lesson::factory()->create();
        $this->message = Message::factory()->create(['lesson_id' => $this->lesson->id]);

        $this->app['router']->put(
            '/dashboard/lessons/{lesson}/messages/{message}',
            [\App\Http\Controllers\MessageController::class, 'update']
        )->name('dashboard.lessons.messages.update')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'subject' => 'Assunto Válido',
            'description' => 'Esta é uma descrição válida.',
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData();

        $response = $this->putJson(route('dashboard.lessons.messages.update', [$this->lesson, $this->message]), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->putJson(route('dashboard.lessons.messages.update', [$this->lesson, $this->message]), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
    }

    public static function validationProvider(): array
    {
        return [
            'subject (ausente)' => ['subject', ''],
            'subject (longo)' => ['subject', str_repeat('a', 256)],
            'description (ausente)' => ['description', ''],
        ];
    }
}
