<section class="avaliacao-section">
    <div class="container text-center pt-5 pb-5">
        @if (Gate::allows('finishedLesson', $lesson))
            <h2 class="mb-3">Você já finalizou o quiz desta aula!</h2>
            <p class="mb-4">Seu certificado está disponível na aba Certificado.</p>
        @else
            <h2 class="mb-3">Quiz de Avaliação do Aprendizado</h2>
            <p class="mb-4">Clique no botão abaixo para iniciar o quiz.</p>

            <button type="button" class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#quizModal" data-lesson-id="{{ $lesson->id ?? 'UNKNOWN_LESSON' }}">
                <i class="bi bi-play-circle-fill"></i> Iniciar Quiz
            </button>
        @endif
    </div>
</section>
<section>
    <div class="modal fade" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true"
        data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content quiz-modal-content">
                <div class="quiz-header">
                    <div id="questionCounter" class="question-counter">Questão ... de ...
                    </div>
                    <div class="progress">
                        <div id="quizProgressBar" class="progress-bar" style="width: 0%">
                        </div>
                    </div>
                    <div id="quizTopic" class="quiz-topic">Carregando Tópico...</div>
                    <button type="button" id="quizCloseBtn" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>

                <div class="question-box">
                    <h2 id="questionText" class="question-text">Carregando Pergunta...
                    </h2>
                </div>

                <div class="quiz-feedback-message mb-3 text-center" id="quizFeedbackMessage" style="display: none;">
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
