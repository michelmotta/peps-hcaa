<?php

namespace Tests\Feature;

use App\Models\Lesson;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected Lesson $lesson;
    protected Topic $topic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create();
        $this->lesson = Lesson::factory()->create();
        $this->topic = Topic::factory()->create(['lesson_id' => $this->lesson->id]);
    }

    #[Test]
    public function um_aluno_autenticado_pode_salvar_o_historico_de_um_topico(): void
    {
        $response = $this->actingAs($this->student)
            ->postJson(route('web.save-history', $this->lesson), [
                'topic_id' => $this->topic->id,
            ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Histórico salvo com sucesso.']);

        $this->assertDatabaseHas('histories', [
            'user_id' => $this->student->id,
            'topic_id' => $this->topic->id,
        ]);
    }

    #[Test]
    public function um_visitante_nao_pode_salvar_o_historico(): void
    {
        $response = $this->postJson(route('web.save-history', $this->lesson), [
            'topic_id' => $this->topic->id,
        ]);

        $response->assertUnauthorized();
    }

    #[Test]
    public function a_requisicao_falha_se_o_topic_id_estiver_ausente(): void
    {
        $response = $this->actingAs($this->student)
            ->postJson(route('web.save-history', $this->lesson), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('topic_id');
    }
}
