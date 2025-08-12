<?php

namespace Tests\Feature\Dashboard;

use App\Enums\ProfileEnum;
use App\Models\Feedback;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FeedbackControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $professor;
    protected User $student;
    protected Lesson $lesson;


    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->professor = User::factory()->create();
        $this->professor->profiles()->attach(ProfileEnum::PROFESSOR->value);

        $this->student = User::factory()->create();

        $this->lesson = Lesson::factory()->create(['user_id' => $this->professor->id]);
    }

    #[Test]
    public function a_pagina_de_feedbacks_exibe_as_estatisticas_corretamente(): void
    {
        Feedback::factory()->create(['lesson_id' => $this->lesson->id, 'rating' => 5]);
        Feedback::factory()->create(['lesson_id' => $this->lesson->id, 'rating' => 4]);
        Feedback::factory()->create(['lesson_id' => $this->lesson->id, 'rating' => 3]);
        Feedback::factory()->create(['lesson_id' => $this->lesson->id, 'rating' => 1]);

        $response = $this->actingAs($this->professor)
            ->get(route('dashboard.lessons.feedbacks.index', $this->lesson));

        $response->assertOk();
        $response->assertViewIs('dashboard.feedbacks.index');
        $response->assertViewHas('totalFeedbacks', 4);
        $response->assertViewHas('averageRating', 3.25);
        $response->assertViewHas('positivePercentage', 50.0);
        $response->assertViewHas('negativePercentage', 25.0);
    }

    #[Test]
    public function a_pagina_de_feedbacks_lida_com_nenhum_feedback_corretamente(): void
    {
        $response = $this->actingAs($this->professor)
            ->get(route('dashboard.lessons.feedbacks.index', $this->lesson));

        $response->assertOk();
        $response->assertViewHas('totalFeedbacks', 0);
        $response->assertViewHas('averageRating', 0);
    }

    #[Test]
    public function um_aluno_pode_enviar_um_feedback_com_sucesso(): void
    {
        $feedbackData = [
            'rating' => 5,
            'comentario' => 'Aula excelente!',
        ];

        $response = $this->actingAs($this->student)
            ->postJson(route('web.feedback.store', $this->lesson), $feedbackData);

        $response->assertOk();
        $response->assertJson(['message' => 'Avaliação enviada com sucesso!']);
        $this->assertDatabaseHas('feedback', [
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->student->id,
            'rating' => 5,
        ]);
    }

    #[Test]
    public function o_envio_de_feedback_falha_com_dados_invalidos(): void
    {
        $invalidData = [
            'rating' => 99,
            'comentario' => 'Comentário',
        ];

        $response = $this->actingAs($this->student)
            ->postJson(route('web.feedback.store', $this->lesson), $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('rating');
    }
}
