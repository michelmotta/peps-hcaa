<section class="avaliacao-section">
    <div class="container text-center">
        @if (Gate::allows('finishedLesson', $lesson))
            <div class="avaliacao-card finished">
                <div class="avaliacao-icon">
                    <i class="bi bi-patch-check-fill"></i>
                </div>
                <h2>Você finalizou o quiz desta aula!</h2>

                @if ($lessonUserData?->score)
                    <div class="quiz-score-display">
                        <div class="score-label">Sua nota final</div>
                        <div class="score-value">{{ $lessonUserData->score }}<span>/10</span></div>
                    </div>
                @endif

                @if (isset($averageScore))
                    <div class="class-average-display">
                        <i class="bi bi-people-fill"></i>
                        <span>Média da turma: <strong>{{ number_format($averageScore, 1, ',', '') }}</strong> /
                            10</span>
                    </div>
                @endif

                <p class="lead mt-3">Seu certificado está te esperando na aba "Certificado".</p>
            </div>
        @else
            <div class="avaliacao-card">
                <div class="avaliacao-icon">
                    <i class="bi bi-card-checklist"></i>
                </div>
                <h2>Teste seus conhecimentos!</h2>
                <p class="lead">Encare o desafio e mostre o que você aprendeu.</p>
                <div class="quiz-button-container" data-lesson-id="{{ $lesson->id ?? 'UNKNOWN_LESSON' }}"
                    data-locked-topic-id="{{ $quizLockedTopicId ?? '' }}">

                    <button type="button" class="btn btn-primary btn-lg start-quiz-btn btn-start-quiz"
                        data-bs-toggle="modal" data-bs-target="#quizModal"
                        data-lesson-id="{{ $lesson->id ?? 'UNKNOWN_LESSON' }}">
                        <i class="bi bi-play-circle-fill me-1"></i> Iniciar Quiz
                    </button>

                    <button type="button" class="btn btn-warning btn-lg start-quiz-btn btn-review-topic"
                        style="display: none;">
                        <i class="bi bi-play-circle-fill me-1"></i> Revisar Tópico Obrigatório
                    </button>
                </div>
            </div>
        @endif
    </div>
</section>
<section>
    <div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content quiz-modal-content">
                <div class="quiz-header">
                    <div id="questionCounter" class="question-counter">Questão ... de ...</div>
                    <div class="progress">
                        <div id="quizProgressBar" class="progress-bar" style="width: 0%"></div>
                    </div>
                    <div id="quizTopic" class="quiz-topic">Carregando Tópico...</div>
                    <button type="button" id="quizCloseBtn" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>

                <div class="question-box">
                    <h2 id="questionText" class="question-text">Carregando Pergunta...</h2>
                </div>

                <div class="quiz-feedback-container" id="quizFeedbackContainer" style="display: none;">
                    <div class="quiz-feedback-message" id="quizFeedbackMessage"></div>
                </div>

                <div id="answerOptionsContainer" class="answer-options"></div>

                <div class="action-buttons">
                    <button id="btnSubmitAnswer" class="btn-submit" disabled>
                        <i class="bi bi-check-circle-fill me-2"></i> Responder
                    </button>
                    <button id="btnNextQuestion" class="btn-next" disabled>
                        <span class="btn-text">Próxima</span>
                        <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                    </button>
                </div>

                <div id="topicFailedVideoInfo" class="p-3" style="display: none;">
                    <h4>Vídeo para Revisão:</h4>
                    <p id="videoTitle"></p>
                    <img id="videoThumbnail" src="" alt="Video Thumbnail"
                        style="max-width: 200px; display: none;">
                    <p><a id="videoLink" href="#" target="_blank" style="display: none;">Assistir vídeo</a></p>
                </div>
            </div>
        </div>
    </div>
</section>
