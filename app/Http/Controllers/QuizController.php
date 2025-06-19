<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonUser;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserTopicQuiz;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class QuizController extends Controller
{
    private const MINIMUM_PASSING_SCORE = 50;

    /**
     * Define the session key for the user's quiz state.
     * Uses user ID and lesson ID to keep sessions separate.
     */
    private function getQuizSessionKey(int $lessonId): string
    {
        return "quiz_state_" . Auth::id() . "_" . $lessonId;
    }

    /**
     * Checks if the current quiz state in the session is valid for the given lesson.
     */
    private function isValidQuizState(?array $state, int $lessonId): bool
    {
        return $state !== null && isset($state['lesson_id']) && $state['lesson_id'] === $lessonId;
    }

    /**
     * Initializes a fresh quiz state for a lesson.
     *
     * @throws \App\Exceptions\NoTopicsFoundException|\Exception If no topics are found for the lesson.
     */
    private function initializeQuizState(Lesson $lesson): array
    {
        $topics = $lesson->topics()->withCount('quizzes')->get();

        if ($topics->isEmpty()) {
            throw new \Exception('Nenhum tópico encontrado para esta aula.');
        }

        $totalQuestionsInLesson = 0;
        $topicQuestionCounts = [];

        foreach ($topics as $topic) {
            $totalQuestionsInLesson += $topic->quizzes_count;
            $topicQuestionCounts[$topic->id] = $topic->quizzes_count;
        }

        return [
            'lesson_id' => $lesson->id,
            'topics_order' => $topics->pluck('id')->toArray(),
            'current_topic_index' => 0,
            'lesson_finished' => false,
            'topics_scores' => [],
            'topics_progress' => [],
            'total_questions_in_lesson' => $totalQuestionsInLesson,
            'topic_question_counts' => $topicQuestionCounts,
        ];
    }

    /**
     * Gets the ID of the current topic based on the quiz state.
     */
    private function getCurrentTopicId(array $quizState): ?int
    {
        return $quizState['topics_order'][$quizState['current_topic_index']] ?? null;
    }

    /**
     * Advances the current topic index in the quiz state.
     */
    private function advanceTopicIndex(array &$quizState): void
    {
        $quizState['current_topic_index']++;
    }

    /**
     * Initializes topic-specific progress data if not already set.
     */
    private function initializeTopicProgressIfNeeded(array &$quizState, Topic $topic): void
    {
        $topicId = $topic->id;
        if (!isset($quizState['topics_progress'][$topicId])) {
            $topicQuizzes = $topic->quizzes()->inRandomOrder()->get();
            $quizState['topics_progress'][$topicId] = [
                'questions_ids' => $topicQuizzes->pluck('id')->toArray(),
                'answered_count' => 0,
                'correct_count' => 0,
                'current_question_index' => 0,
            ];
        }
    }

    /**
     * Retrieves the current question from the database based on topic progress.
     */
    private function getCurrentQuestion(array $topicProgress): ?Quiz
    {
        $currentQuestionIndex = $topicProgress['current_question_index'];
        $questionsIds = $topicProgress['questions_ids'];

        // Ensure index is valid before trying to access it
        if (isset($questionsIds[$currentQuestionIndex])) {
            return Quiz::find($questionsIds[$currentQuestionIndex]);
        }
        return null;
    }

    /**
     * Calculates the percentage score for a given topic progress.
     */
    private function calculateScore(array $topicProgress): float
    {
        $answeredCount = $topicProgress['answered_count'];
        $correctCount = $topicProgress['correct_count'];
        return $answeredCount > 0 ? ($correctCount / $answeredCount) * 100 : 0;
    }

    /**
     * Returns a standardized JSON error response.
     */
    private function responseError(string $message, int $statusCode = 400): JsonResponse
    {
        return response()->json(['status' => 'error', 'message' => $message], $statusCode);
    }

    /**
     * Returns a standardized JSON response for a question.
     */
    private function responseQuestion(
        Lesson $lesson,
        Topic $currentTopic,
        array $topicProgress,
        Quiz $question,
        int $overallCurrentQuestionNumber,
        int $totalQuestionsInLesson,
        int $overallProgressInLesson
    ): JsonResponse {

        $currentQuestionIndexInTopic = $topicProgress['current_question_index'];
        $totalQuestionsInTopic = count($topicProgress['questions_ids']);
        $progressInTopic = $totalQuestionsInTopic > 0 ? round((($currentQuestionIndexInTopic + 1) / $totalQuestionsInTopic) * 100) : 0;

        return response()->json([
            'status' => 'question',
            'lesson_id' => $lesson->id,
            'topic_id' => $currentTopic->id,
            'topic_title' => $currentTopic->title,
            'overall_current_question_number' => $overallCurrentQuestionNumber,
            'total_questions_in_lesson' => $totalQuestionsInLesson,
            'overall_progress_in_lesson' => $overallProgressInLesson,
            'current_question_index' => $currentQuestionIndexInTopic + 1,
            'total_questions_in_topic' => $totalQuestionsInTopic,
            'progress_in_topic' => $progressInTopic,

            'question' => [
                'id' => $question->id,
                'text' => $question->question,
                'options' => $question->options,
            ],
        ]);
    }

    /**
     * Returns a standardized JSON response for quiz completion.
     */
    private function responseFinished(): JsonResponse
    {
        return response()->json([
            'status' => 'finished',
            'finished' => true,
            'message' => 'Parabéns! Você concluiu a avaliação de todas as aulas.',
        ]);
    }

    /**
     * Returns a standardized JSON response for topic completion and readiness for next.
     */
    private function responseNextTopicReady(bool $isCorrect, string $correctAnswer): JsonResponse
    {
        return response()->json([
            'status' => 'next_topic_ready',
            'is_correct' => $isCorrect,
            'correct_answer' => $correctAnswer,
            'message' => 'Tópico concluído com sucesso! Prepare-se para o próximo.',
        ]);
    }

    /**
     * Returns a standardized JSON response for a topic failure.
     * Includes Topic and Video information for the frontend to handle.
     */
    private function responseTopicFailed(bool $isCorrect, string $correctAnswer, float $score, Topic $topic, Lesson $lesson): JsonResponse
    {
        $topic->loadMissing('video');

        $videoData = null;
        if ($topic->video) {
            $videoData = [
                'id' => $topic->video->id,
                'name' => $topic->video->name,
                'path' => $topic->video->path,
                'thumbnail_path' => $topic->video->thumbnail_path,
                'duration' => $topic->video->duration,
            ];
        }

        return response()->json([
            'status' => 'topic_failed',
            'is_correct' => $isCorrect,
            'correct_answer' => $correctAnswer,
            'score' => round($score),
            'message' => "Você acertou apenas " . round($score) . "% das perguntas do tópico '{$topic->title}'. Assista ao vídeo novamente e tente outra vez.",
            'topic' => [
                'id' => $topic->id,
                'title' => $topic->title,
            ],
            'video' => $videoData,
            'lesson_id' => $lesson->id,
        ]);
    }

    /**
     * Finalizes the lesson quiz, calculates overall score, and updates lesson_user table.
     */
    private function finalizeLessonQuiz(Lesson $lesson, User $user, string $sessionKey, array $quizState): void
    {
        $averageScore = 0;
        if (count($quizState['topics_scores']) > 0) {
            $averageScore = array_sum($quizState['topics_scores']) / count($quizState['topics_scores']);
        }

        DB::transaction(function () use ($lesson, $user, $averageScore) {
            LessonUser::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                ],
                [
                    'score' => round($averageScore, 2),
                    'finished' => true,
                    'finished_at' => now(),
                ]
            );
        });
        Session::forget($sessionKey);
    }

    public function getNextQuestion(Lesson $lesson): JsonResponse
    {
        $sessionKey = $this->getQuizSessionKey($lesson->id);
        $quizState = Session::get($sessionKey);

        try {
            if (!$this->isValidQuizState($quizState, $lesson->id)) {
                $quizState = $this->initializeQuizState($lesson);
                Session::put($sessionKey, $quizState);
            }

            while (true) {
                $currentTopicId = $this->getCurrentTopicId($quizState);

                if ($currentTopicId === null) {
                    $this->finalizeLessonQuiz($lesson, Auth::user(), $sessionKey, $quizState);
                    return $this->responseFinished();
                }

                $currentTopic = Topic::find($currentTopicId);
                if (!$currentTopic) {
                    $this->advanceTopicIndex($quizState);
                    Session::put($sessionKey, $quizState);
                    continue;
                }

                $this->initializeTopicProgressIfNeeded($quizState, $currentTopic);

                $topicProgress = $quizState['topics_progress'][$currentTopicId];

                if (empty($topicProgress['questions_ids'])) {
                    $quizState['topics_scores'][$currentTopicId] = 100;
                    $this->advanceTopicIndex($quizState);
                    Session::put($sessionKey, $quizState);
                    continue;
                }

                $question = $this->getCurrentQuestion($topicProgress);
                if (!$question) {
                    $quizState['topics_progress'][$currentTopicId]['current_question_index']++;
                    Session::put($sessionKey, $quizState); // Save state after advancing
                    continue;
                }

                $overallCurrentQuestionNumber = 0;
                for ($i = 0; $i < $quizState['current_topic_index']; $i++) {
                    $previousTopicId = $quizState['topics_order'][$i];
                    $overallCurrentQuestionNumber += ($quizState['topic_question_counts'][$previousTopicId] ?? 0);
                }
                $overallCurrentQuestionNumber += $topicProgress['current_question_index'] + 1;

                $totalQuestionsInLesson = $quizState['total_questions_in_lesson'] ?? 0;
                $overallProgressInLesson = $totalQuestionsInLesson > 0 ?
                    round(($overallCurrentQuestionNumber / $totalQuestionsInLesson) * 100) : 0;

                Session::put($sessionKey, $quizState);
                return $this->responseQuestion(
                    $lesson,
                    $currentTopic,
                    $topicProgress,
                    $question,
                    $overallCurrentQuestionNumber,
                    $totalQuestionsInLesson,
                    $overallProgressInLesson
                );
            }
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    public function submitAnswer(Request $request, Lesson $lesson): JsonResponse
    {
        $request->validate([
            'question_id' => 'required|exists:quizzes,id',
            'selected_option' => 'required|string',
        ]);

        $sessionKey = $this->getQuizSessionKey($lesson->id);
        $quizState = Session::get($sessionKey);
        $user = Auth::user();

        if (!$user) {
            return $this->responseError('Usuário não autenticado.', 401);
        }

        try {
            if (!$this->isValidQuizState($quizState, $lesson->id)) {
                return $this->responseError('Sessão do quiz inválida. Por favor, reinicie.', 400);
            }

            $currentTopicId = $this->getCurrentTopicId($quizState);
            if ($currentTopicId === null) {
                // Se não há um tópico ativo, tenta finalizar caso haja algum dado pendente
                if (!empty($quizState['topics_scores'])) {
                    $this->finalizeLessonQuiz($lesson, $user, $sessionKey, $quizState);
                }
                return $this->responseError('Não há mais perguntas. O quiz já foi concluído.', 400);
            }

            if (!isset($quizState['topics_progress'][$currentTopicId])) {
                // Lógica de recuperação de estado se o progresso do tópico não for encontrado
                $currentTopicForInit = Topic::find($currentTopicId);
                if ($currentTopicForInit) {
                    $this->initializeTopicProgressIfNeeded($quizState, $currentTopicForInit);
                } else {
                    return $this->responseError('Tópico atual não encontrado. Reinicie o quiz.', 400);
                }
            }

            $question = Quiz::find($request->question_id);
            if (!$question || (int)$question->topic_id !== (int)$currentTopicId) {
                return $this->responseError('Pergunta inválida ou não pertence ao tópico atual.', 400);
            }

            $topicProgress = $quizState['topics_progress'][$currentTopicId];

            $expectedQuestionId = $topicProgress['questions_ids'][$topicProgress['current_question_index']] ?? null;
            if ((int)$request->question_id !== (int)$expectedQuestionId) {
                return $this->responseError('Pergunta fora de sequência. Não atualize a página no meio do quiz.', 400);
            }

            $isCorrect = strtolower(trim($request->selected_option)) === strtolower(trim($question->correct));

            // Atualiza o progresso na sessão
            $quizState['topics_progress'][$currentTopicId]['answered_count']++;
            if ($isCorrect) {
                $quizState['topics_progress'][$currentTopicId]['correct_count']++;
            }
            $quizState['topics_progress'][$currentTopicId]['current_question_index']++;

            $topicFinished = $quizState['topics_progress'][$currentTopicId]['current_question_index'] >= count($quizState['topics_progress'][$currentTopicId]['questions_ids']);

            if ($topicFinished) {
                // Reatribui topicProgress após as atualizações
                $topicProgress = $quizState['topics_progress'][$currentTopicId];
                $score = $this->calculateScore($topicProgress);

                $currentTopic = Topic::find($currentTopicId);
                if (!$currentTopic) {
                    return $this->responseError('Tópico não encontrado ao finalizar.', 500);
                }

                $attemptNumber = UserTopicQuiz::where('user_id', $user->id)
                    ->where('topic_id', $currentTopicId)
                    ->count() + 1;

                UserTopicQuiz::create([
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                    'topic_id' => $currentTopicId,
                    'correct_count' => $topicProgress['correct_count'],
                    'total_count' => $topicProgress['answered_count'],
                    'score' => $score,
                    'passed' => $score >= self::MINIMUM_PASSING_SCORE,
                    'attempt_number' => $attemptNumber,
                ]);

                if ($score < self::MINIMUM_PASSING_SCORE) {
                    unset($quizState['topics_progress'][$currentTopicId]); // Reseta o progresso do tópico falho
                    Session::put($sessionKey, $quizState);
                    return $this->responseTopicFailed($isCorrect, $question->correct, $score, $currentTopic, $lesson);
                }

                // Tópico Aprovado
                $quizState['topics_scores'][$currentTopicId] = round($score, 2);
                $this->advanceTopicIndex($quizState);

                // Verifica se a aula inteira terminou
                if ($this->getCurrentTopicId($quizState) === null) {
                    $this->finalizeLessonQuiz($lesson, $user, $sessionKey, $quizState);
                    return $this->responseFinished();
                }

                Session::put($sessionKey, $quizState);
                return $this->responseNextTopicReady($isCorrect, $question->correct);
            }

            // Se o tópico não terminou, apenas salva o estado e retorna o status da resposta
            Session::put($sessionKey, $quizState);
            return response()->json([
                'status' => 'answer_received',
                'is_correct' => $isCorrect,
                'correct_answer' => $question->correct,
                'message' => $isCorrect ? 'Resposta correta!' : 'Resposta incorreta.',
            ]);
        } catch (Exception $e) {
            return $this->responseError($e->getMessage(), 500);
        }
    }

    /**
     * Clears the user's quiz session for a specific lesson.
     */
    public function clearSession(Lesson $lesson): JsonResponse
    {
        try {
            $sessionKey = $this->getQuizSessionKey($lesson->id);
            Session::forget($sessionKey);

            return response()->json([
                'status' => 'success',
                'message' => 'Sessão do quiz limpa com sucesso.',
            ]);
        } catch (Exception $e) {
            return $this->responseError('Ocorreu um erro ao limpar a sessão do quiz.', 500);
        }
    }

    public function generateCertificate(Lesson $lesson)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Gate::allows('generateCertificate', $lesson)) {
            abort(403, 'Você não tem permissão para gerar este certificado.');
        }

        $lesson = $user->subscribedLessons()->where('lessons.id', $lesson->id)->firstOrFail();

        $pdf = Pdf::loadView('web.includes.certificate', [
            'user' => $user,
            'lesson' => $lesson,
            'date' => now()->translatedFormat('d \\d\\e F \\d\\e Y'),
        ])
            ->setPaper('a4', 'landscape');;

        return $pdf->download('certificado-' . $lesson->id . '.pdf');
    }
}
