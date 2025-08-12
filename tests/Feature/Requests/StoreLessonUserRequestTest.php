<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Controllers\LessonUserController;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreLessonUserRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $student;
    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->student = User::factory()->create();
        $this->lesson = Lesson::factory()->create();

        $this->app['router']->post(
            '/dashboard/lessons/{lesson}/subscriptions',
            [LessonUserController::class, 'store']
        )->name('dashboard.lessons.subscriptions.store')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'finished' => false,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([
            'finished_at' => null,
            'score' => null,
        ]);

        $response = $this->postJson(route('dashboard.lessons.subscriptions.store', $this->lesson), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->postJson(route('dashboard.lessons.subscriptions.store', $this->lesson), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
    }

    public static function validationProvider(): array
    {
        return [
            'user_id (ausente)' => ['user_id', ''],
            'lesson_id (ausente)' => ['lesson_id', ''],
            'finished (ausente)' => ['finished', ''],
            'created_at (ausente)' => ['created_at', ''],
        ];
    }
}
