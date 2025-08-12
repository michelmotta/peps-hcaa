<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Models\Lesson;
use App\Models\LessonUser;
use App\Models\Profile;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QuizControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $student;
    private Lesson $lesson;
    private Topic $topic1;
    private Topic $topic2;
    private Quiz $q1_t1;
    private Quiz $q2_t1;
    private Quiz $q1_t2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::factory()->create();

        $this->lesson = Lesson::factory()->create();
        $this->topic1 = Topic::factory()->create(['lesson_id' => $this->lesson->id]);
        $this->topic2 = Topic::factory()->create(['lesson_id' => $this->lesson->id]);

        $this->q1_t1 = Quiz::factory()->create(['topic_id' => $this->topic1->id, 'correct' => 'A']);
        $this->q2_t1 = Quiz::factory()->create(['topic_id' => $this->topic1->id, 'correct' => 'B']);
        $this->q1_t2 = Quiz::factory()->create(['topic_id' => $this->topic2->id, 'correct' => 'C']);
    }

    private function getSessionKey(): string
    {
        return "quiz_state_" . $this->student->id . "_" . $this->lesson->id;
    }

    #[Test]
    public function get_next_question_initializes_quiz_and_returns_first_question(): void
    {
        $response = $this->actingAs($this->student)
            ->getJson(route('web.quiz.nextQuestion', $this->lesson));

        $response->assertOk();
        $response->assertJson(['status' => 'question']);
        $this->assertNotNull(Session::get($this->getSessionKey()));
    }

    #[Test]
    public function get_next_question_returns_error_if_lesson_has_no_topics(): void
    {
        $lessonWithoutTopics = Lesson::factory()->create();
        $response = $this->actingAs($this->student)
            ->getJson(route('web.quiz.nextQuestion', $lessonWithoutTopics));

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error', 'message' => 'Nenhum tópico encontrado para esta aula.']);
    }

    #[Test]
    public function get_next_question_skips_topic_with_no_quizzes(): void
    {
        $topicWithNoQuiz = Topic::factory()->create(['lesson_id' => $this->lesson->id]);
        $this->lesson->topics()->saveMany([$topicWithNoQuiz, $this->topic1, $this->topic2]);

        $response = $this->actingAs($this->student)
            ->getJson(route('web.quiz.nextQuestion', $this->lesson));

        $response->assertOk();
        $response->assertJson(['status' => 'question', 'topic_id' => $this->topic1->id]);
    }

    #[Test]
    public function get_next_question_skips_deleted_topic_mid_quiz(): void
    {
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));

        $this->topic1->quizzes()->delete();
        $this->topic1->delete();

        $response = $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));

        $response->assertOk();
        $response->assertJson(['status' => 'question', 'topic_id' => $this->topic2->id]);
    }

    #[Test]
    public function submit_answer_handles_correct_answer(): void
    {
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));
        $quizState = Session::get($this->getSessionKey());
        $currentQuestionId = $quizState['topics_progress'][$this->topic1->id]['questions_ids'][0];
        $question = Quiz::find($currentQuestionId);

        $response = $this->actingAs($this->student)->postJson(route('web.quiz.submitAnswer', $this->lesson), [
            'question_id' => $question->id,
            'selected_option' => $question->correct,
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'answer_received', 'is_correct' => true]);
    }

    #[Test]
    public function submit_answer_handles_incorrect_answer(): void
    {
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));
        $quizState = Session::get($this->getSessionKey());
        $currentQuestionId = $quizState['topics_progress'][$this->topic1->id]['questions_ids'][0];

        $response = $this->actingAs($this->student)->postJson(route('web.quiz.submitAnswer', $this->lesson), [
            'question_id' => $currentQuestionId,
            'selected_option' => 'WRONG_ANSWER',
        ]);

        $response->assertOk();
        $response->assertJson(['status' => 'answer_received', 'is_correct' => false]);
    }

    #[Test]
    public function submit_answer_advances_to_next_topic_on_pass(): void
    {
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));
        $quizState = Session::get($this->getSessionKey());
        $questions = $quizState['topics_progress'][$this->topic1->id]['questions_ids'];
        $lastResponse = null;
        foreach ($questions as $qId) {
            $q = Quiz::find($qId);
            $lastResponse = $this->actingAs($this->student)->postJson(route('web.quiz.submitAnswer', $this->lesson), [
                'question_id' => $q->id,
                'selected_option' => $q->correct,
            ]);
        }

        $lastResponse->assertOk();
        $lastResponse->assertJson(['status' => 'next_topic_ready']);
    }

    #[Test]
    public function submit_answer_fails_topic_on_low_score(): void
    {
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));
        $quizState = Session::get($this->getSessionKey());
        $questions = $quizState['topics_progress'][$this->topic1->id]['questions_ids'];
        $lastResponse = null;
        foreach ($questions as $qId) {
            $lastResponse = $this->actingAs($this->student)->postJson(route('web.quiz.submitAnswer', $this->lesson), [
                'question_id' => $qId,
                'selected_option' => 'WRONG_ANSWER',
            ]);
        }

        $lastResponse->assertOk();
        $lastResponse->assertJson(['status' => 'topic_failed', 'score' => 0]);
        $this->assertDatabaseHas('user_topic_quizzes', ['user_id' => $this->student->id, 'topic_id' => $this->topic1->id, 'passed' => false]);
    }

    #[Test]
    public function submit_answer_finishes_lesson_on_completion(): void
    {
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));
        $quizState = Session::get($this->getSessionKey());
        $questions1 = $quizState['topics_progress'][$this->topic1->id]['questions_ids'];
        foreach ($questions1 as $qId) {
            $q = Quiz::find($qId);
            $this->actingAs($this->student)->postJson(route('web.quiz.submitAnswer', $this->lesson), ['question_id' => $q->id, 'selected_option' => $q->correct]);
        }
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));
        $quizState = Session::get($this->getSessionKey());
        $questions2 = $quizState['topics_progress'][$this->topic2->id]['questions_ids'];
        $response = null;
        foreach ($questions2 as $qId) {
            $q = Quiz::find($qId);
            $response = $this->actingAs($this->student)->postJson(route('web.quiz.submitAnswer', $this->lesson), ['question_id' => $q->id, 'selected_option' => $q->correct]);
        }

        $response->assertOk();
        $response->assertJson(['status' => 'finished']);
        $this->assertDatabaseHas('lesson_user', ['user_id' => $this->student->id, 'lesson_id' => $this->lesson->id, 'finished' => true, 'score' => 100]);
    }

    #[Test]
    public function submit_answer_fails_for_invalid_session(): void
    {
        $response = $this->actingAs($this->student)->postJson(route('web.quiz.submitAnswer', $this->lesson), [
            'question_id' => $this->q1_t1->id,
            'selected_option' => 'A',
        ]);
        $response->assertStatus(400);
        $response->assertJson(['status' => 'error', 'message' => 'Sessão do quiz inválida. Por favor, reinicie.']);
    }

    #[Test]
    public function submit_answer_fails_for_out_of_sequence_question(): void
    {
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));
        $quizState = Session::get($this->getSessionKey());
        $questionIds = $quizState['topics_progress'][$this->topic1->id]['questions_ids'];
        $this->assertGreaterThan(1, count($questionIds));
        $secondQuestionId = $questionIds[1];
        $secondQuestion = Quiz::find($secondQuestionId);

        $response = $this->actingAs($this->student)->postJson(route('web.quiz.submitAnswer', $this->lesson), [
            'question_id' => $secondQuestion->id,
            'selected_option' => $secondQuestion->correct,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['status' => 'error', 'message' => 'Pergunta fora de sequência. Não atualize a página no meio do quiz.']);
    }

    #[Test]
    public function submit_answer_fails_if_question_not_in_current_topic(): void
    {
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));

        $response = $this->actingAs($this->student)->postJson(route('web.quiz.submitAnswer', $this->lesson), [
            'question_id' => $this->q1_t2->id,
            'selected_option' => $this->q1_t2->correct,
        ]);

        $response->assertStatus(400);
        $response->assertJson(['status' => 'error', 'message' => 'Pergunta inválida ou não pertence ao tópico atual.']);
    }

    #[Test]
    public function submit_answer_fails_if_user_not_authenticated(): void
    {
        $response = $this->postJson(route('web.quiz.submitAnswer', $this->lesson), [
            'question_id' => $this->q1_t1->id,
            'selected_option' => 'A',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    #[Test]
    public function submit_answer_fails_if_quiz_is_already_finished(): void
    {
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));
        $sessionKey = $this->getSessionKey();
        $quizState = Session::get($sessionKey);
        $quizState['current_topic_index'] = count($quizState['topics_order']);
        Session::put($sessionKey, $quizState);

        $response = $this->actingAs($this->student)->postJson(route('web.quiz.submitAnswer', $this->lesson), [
            'question_id' => $this->q1_t1->id,
            'selected_option' => 'A',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['status' => 'error', 'message' => 'Não há mais perguntas. O quiz já foi concluído.']);
    }

    #[Test]
    public function clear_session_removes_quiz_state(): void
    {
        $this->actingAs($this->student)->getJson(route('web.quiz.nextQuestion', $this->lesson));
        $this->assertNotNull(Session::get($this->getSessionKey()));

        $response = $this->actingAs($this->student)->postJson(route('web.quiz.clearSession', $this->lesson));

        $response->assertOk();
        $response->assertJson(['status' => 'success']);
        $this->assertNull(Session::get($this->getSessionKey()));
    }
}
