<?php

namespace Tests\Unit\Models;

use App\Models\Feedback;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_metodo_toSearchableArray_retorna_os_dados_corretos(): void
    {
        $feedback = Feedback::factory()->create([
            'comentario' => 'Este é um comentário para a busca.',
        ]);

        $expectedArray = [
            'comentario' => 'Este é um comentário para a busca.',
        ];

        $this->assertEquals($expectedArray, $feedback->toSearchableArray());
    }

    #[Test]
    public function o_feedback_pertence_a_um_usuario(): void
    {
        $user = User::factory()->create();
        $feedback = Feedback::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $feedback->user);
        $this->assertEquals($user->id, $feedback->user->id);
    }

    #[Test]
    public function o_feedback_pertence_a_uma_aula(): void
    {
        $lesson = Lesson::factory()->create();
        $feedback = Feedback::factory()->create(['lesson_id' => $lesson->id]);

        $this->assertInstanceOf(Lesson::class, $feedback->lesson);
        $this->assertEquals($lesson->id, $feedback->lesson->id);
    }
}
