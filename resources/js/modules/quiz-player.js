export function initQuizPlayer() {
    const btnNext = document.getElementById('btnNextQuestion');

    if (!btnNext) {
        return;
    }

    const ui = {
        modal: document.getElementById('quizModal'),
        counter: document.getElementById('questionCounter'),
        progressBar: document.getElementById('quizProgressBar'),
        topic: document.getElementById('quizTopic'),
        question: document.getElementById('questionText'),
        optionsContainer: document.getElementById('answerOptionsContainer'),
        btnSubmit: document.getElementById('btnSubmitAnswer'),
        btnNext: btnNext,
        btnNextText: btnNext.querySelector('.btn-text'),
        feedbackContainer: document.getElementById('quizFeedbackContainer'),
        feedbackMessage: document.getElementById('quizFeedbackMessage'),
    };

    if (!ui.modal) return;

    const state = {
        lessonId: null,
        questionId: null,
        selectedKey: null,
        isAnswered: false,
    };

    let topicToPlayAfterClose = null;
    let quizTriggerButton = null;

    function showFeedback(message, type) {
        ui.feedbackMessage.innerHTML = `<i class="bi ${type === 'correct' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'} feedback-icon"></i> ${message}`;
        ui.feedbackMessage.className = `quiz-feedback-message ${type}`;
        ui.feedbackContainer.style.display = 'block';
    }

    function hideFeedback() {
        ui.feedbackContainer.style.display = 'none';
    }

    function resetUIForNewQuestion() {
        state.isAnswered = false;
        state.selectedKey = null;
        hideFeedback();
        ui.optionsContainer.innerHTML = '';
        ui.btnSubmit.style.display = 'inline-flex';
        ui.btnSubmit.disabled = true;
        ui.btnNext.style.display = 'none';
        ui.btnNext.disabled = true;
        ui.btnNextText.textContent = 'Próxima';
        ui.btnNext.onclick = fetchNextQuestion;
    }

    function renderQuestion(data) {
        state.questionId = data.question.id;
        ui.question.textContent = data.question.text;
        ui.topic.textContent = `Tópico: ${data.topic_title}`;
        ui.counter.textContent = `Questão ${data.overall_current_question_number} de ${data.total_questions_in_lesson}`;
        ui.progressBar.style.width = `${data.overall_progress_in_lesson}%`;
        ui.optionsContainer.innerHTML = '';
        data.question.options.forEach(optionObject => {
            const key = Object.keys(optionObject)[0];
            const text = optionObject[key];
            const buttonHtml = `<button class="btn-answer" data-key="${key}"><span class="option-label">${key}</span> ${text}</button>`;
            ui.optionsContainer.insertAdjacentHTML('beforeend', buttonHtml);
        });
    }

    function showAnswerResult(data) {
        state.isAnswered = true;
        const { is_correct, message } = data;
        ui.optionsContainer.querySelectorAll('.btn-answer').forEach(btn => {
            const btnKey = btn.dataset.key;
            if (is_correct) {
                if (btnKey == state.selectedKey) btn.classList.add('correct');
                else btn.classList.add('disabled-option');
            } else {
                if (btnKey == state.selectedKey) btn.classList.add('incorrect');
                else btn.classList.add('disabled-option');
            }
            btn.classList.remove('active');
        });
        showFeedback(message, is_correct ? 'correct' : 'incorrect');
    }

    function renderQuizFinished(data) {
        hideFeedback();
        ui.question.textContent = data.message;
        ui.topic.textContent = 'Parabéns!';
        ui.optionsContainer.innerHTML = '';
        ui.progressBar.style.width = '100%';
        ui.counter.textContent = 'Completo!';
        showFeedback(data.message, 'correct');
        ui.btnNextText.textContent = 'Fechar Quiz';
        ui.btnNext.onclick = () => {
            bootstrap.Modal.getInstance(ui.modal).hide();
            window.location.reload();
        };
    }

    function processApiResponse(data) {
        if (data.status !== 'question' && data.status !== 'error') {
            ui.btnSubmit.style.display = 'none';
            ui.btnNext.style.display = 'inline-flex';
            ui.btnNext.disabled = false;
        }
        switch (data.status) {
            case 'question':
                renderQuestion(data);
                break;
            case 'answer_received':
            case 'next_topic_ready':
                showAnswerResult(data);
                break;
            case 'topic_failed':
                showAnswerResult(data);
                ui.btnNextText.textContent = 'Revisar Vídeo da Aula';
                ui.btnNext.onclick = () => {
                    ui.btnNext.blur();
                    topicToPlayAfterClose = data.topic.id;
                    bootstrap.Modal.getInstance(ui.modal).hide();
                };
                break;
            case 'finished':
                renderQuizFinished(data);
                break;
            case 'error':
                showFeedback(data.message, 'incorrect');
                ui.btnSubmit.disabled = false;
                break;
            default:
                console.error('Unknown response status:', data.status);
        }
    }

    async function submitAnswer() {
        if (!state.selectedKey) return;
        ui.btnSubmit.disabled = true;
        try {
            const response = await axios.post(`/aula/${state.lessonId}/quiz/submit-answer`, {
                question_id: state.questionId,
                selected_option: state.selectedKey
            });
            processApiResponse(response.data);
        } catch (error) {
            console.error('Error submitting answer:', error);
            showFeedback(error.response?.data?.message || 'Erro ao enviar resposta.', 'incorrect');
            ui.btnSubmit.disabled = false;
        }
    }

    async function fetchNextQuestion() {
        resetUIForNewQuestion();
        ui.question.textContent = 'Carregando...';
        try {
            const response = await axios.get(`/aula/${state.lessonId}/quiz/next-question`);
            processApiResponse(response.data);
        } catch (error) {
            console.error('Error fetching question:', error);
            ui.question.textContent = 'Não foi possível carregar a pergunta.';
            showFeedback(error.response?.data?.message || 'Erro de comunicação com o servidor.', 'incorrect');
        }
    }

    ui.optionsContainer.addEventListener('click', (event) => {
        if (state.isAnswered) return;
        const button = event.target.closest('.btn-answer');
        if (!button) return;
        ui.optionsContainer.querySelector('.btn-answer.active')?.classList.remove('active');
        button.classList.add('active');
        state.selectedKey = button.dataset.key;
        ui.btnSubmit.disabled = false;
    });

    ui.btnSubmit.addEventListener('click', submitAnswer);

    ui.modal.addEventListener('show.bs.modal', (event) => {
        quizTriggerButton = event.relatedTarget;

        const button = event.relatedTarget;
        if (button?.dataset.lessonId) {
            state.lessonId = button.dataset.lessonId;
            fetchNextQuestion();
        } else {
            console.error('Lesson ID not found on triggering button.');
        }
    });

    ui.modal.addEventListener('hidden.bs.modal', () => {
        const isTopicFailure = !!topicToPlayAfterClose;

        if (isTopicFailure) {
            const topicId = topicToPlayAfterClose;
            topicToPlayAfterClose = null;
            const topicItem = document.querySelector(`.topic-item[data-topic-id="${topicId}"]`);
            if (topicItem) {
                topicItem.click();
                const videoContainer = document.querySelector('.video-container');
                if (videoContainer) {
                    videoContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }

        resetUIForNewQuestion();

        if (quizTriggerButton) {
            quizTriggerButton.focus();
        }
    });

    document.querySelectorAll('.quiz-button-container').forEach(container => {
        const lessonId = container.dataset.lessonId;
        // Lê o ID do tópico bloqueado diretamente do atributo data
        const lockedTopicId = container.dataset.lockedTopicId;

        const quizButton = container.querySelector('.btn-start-quiz');
        const reviewButton = container.querySelector('.btn-review-topic');

        if (!quizButton || !reviewButton) return;

        if (lockedTopicId) {
            quizButton.style.display = 'none';
            reviewButton.style.display = 'inline-flex';

            reviewButton.onclick = () => {
                const topicItem = document.querySelector(`.topic-item[data-topic-id="${lockedTopicId}"]`);
                if (topicItem) {
                    topicItem.click();
                    const videoContainer = document.querySelector('.video-container');
                    if (videoContainer) {
                        videoContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            };

            // Adiciona o destaque no tópico da playlist
            const topicItem = document.querySelector(`.topic-item[data-topic-id="${lockedTopicId}"]`);
            if (topicItem) {
                topicItem.classList.add('must-watch');
                topicItem.title = "Você precisa assistir este tópico para reabrir o quiz.";
            }
        } else {
            quizButton.style.display = 'inline-flex';
            reviewButton.style.display = 'none';
        }
    });

    document.addEventListener('quiz:unlocked', (event) => {
        const { lessonId, topicId } = event.detail;

        const container = document.querySelector(`.quiz-button-container[data-lesson-id="${lessonId}"]`);
        if (!container) return;

        // Atualiza o atributo data para refletir o novo estado
        container.dataset.lockedTopicId = '';

        const quizButton = container.querySelector('.btn-start-quiz');
        const reviewButton = container.querySelector('.btn-review-topic');

        // Troca os botões
        if (reviewButton) reviewButton.style.display = 'none';
        if (quizButton) quizButton.style.display = 'inline-flex';

        // Remove o destaque do tópico
        const topicItem = document.querySelector(`.topic-item[data-topic-id="${topicId}"]`);
        if (topicItem) {
            topicItem.classList.remove('must-watch');
            topicItem.title = "";
        }
    });
}