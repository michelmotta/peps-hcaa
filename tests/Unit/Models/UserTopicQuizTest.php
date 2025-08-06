<?php

namespace Tests\Unit\Models;

use App\Models\Lesson;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserTopicQuiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTopicQuizTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function model_pertence_a_um_usuario(): void
    {
        $user = User::factory()->create();
        $quizResult = UserTopicQuiz::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $quizResult->user);
        $this->assertEquals($user->id, $quizResult->user->id);
    }

    #[Test]
    public function model_pertence_a_um_topico(): void
    {
        $topic = Topic::factory()->create();
        $quizResult = UserTopicQuiz::factory()->create(['topic_id' => $topic->id]);

        $this->assertInstanceOf(Topic::class, $quizResult->topic);
        $this->assertEquals($topic->id, $quizResult->topic->id);
    }

    #[Test]
    public function model_pertence_a_uma_aula(): void
    {
        $lesson = Lesson::factory()->create();
        $quizResult = UserTopicQuiz::factory()->create(['lesson_id' => $lesson->id]);

        $this->assertInstanceOf(Lesson::class, $quizResult->lesson);
        $this->assertEquals($lesson->id, $quizResult->lesson->id);
    }
}
