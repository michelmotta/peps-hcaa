<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Controllers\LessonUserController;
use App\Models\Lesson;
use App\Models\LessonUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateLessonUserRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $student;
    private Lesson $lesson;
    private LessonUser $subscription;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->student = User::factory()->create();
        $this->lesson = Lesson::factory()->create();
        $this->lesson->subscriptions()->attach($this->student->id);

        $this->subscription = LessonUser::where('user_id', $this->student->id)
            ->where('lesson_id', $this->lesson->id)
            ->first();

        $this->app['router']->put(
            '/dashboard/lessons/{lesson}/subscriptions/{subscription}',
            [LessonUserController::class, 'update']
        )->name('dashboard.lessons.subscriptions.update')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id,
            'finished' => true,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([
            'finished_at' => now()->format('Y-m-d H:i:s'),
            'score' => 95.5,
        ]);

        $response = $this->putJson(route('dashboard.lessons.subscriptions.update', [$this->lesson, $this->subscription]), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $this->actingAs($this->user);
        $data = $this->getValidData([$field => $value]);

        $response = $this->putJson(route('dashboard.lessons.subscriptions.update', [$this->lesson, $this->subscription]), $data);

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
