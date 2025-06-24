// ─── Imports Globais ─────────────────────────────────────────────────────────
import './bootstrap';
import './theme.min';

import Swal from 'sweetalert2';
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";

import TomSelect from 'tom-select';
import "tom-select/dist/css/tom-select.bootstrap5.css";

import { Dropzone } from "dropzone";
import "dropzone/dist/dropzone.css";

import IMask from 'imask';

import { initQuizEditor } from './quiz-editor';

import Plyr from 'plyr';
import 'plyr/dist/plyr.css';

// ─── Fancybox Init ───────────────────────────────────────────────────────────
Fancybox.bind('[data-fancybox]');


// ─── Helpers ─────────────────────────────────────────────────────────────────
window.confirmDelete = function (formId) {
    Swal.fire({
        title: 'Você quer mesmo apagar?',
        text: "Esta ação não poderá ser desfeita!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
};


// ─── Dom Ready ───────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    initQuillSync();
    initTomSelect();
    initDateMasks();
    initDropzoneUploader();
    initDeleteAttachmentButtons();
    initDoubtFormSubmissions();
    initPlyrWithTopics();
    updateProgressBar();
    initQuizJson();
});

window.addEventListener('load', () => {
    feedbackAlert();
});

// ─── Funções Específicas ─────────────────────────────────────────────────────
function feedbackAlert() {
    const alert = document.querySelector('.custom-alert');
    if (alert) {
        setTimeout(() => {
            alert.classList.add('alert-visible');
        }, 200);
    }
}

function initQuizJson() {
    const quizJson = document.getElementById('avaliacaoJson')?.value;
    if (quizJson) {
        try {
            const data = JSON.parse(quizJson);
            initQuizEditor(data);
        } catch (e) {
            console.error('Erro ao carregar avaliação salva:', e);
        }
    }
}

function initQuillSync() {
    const biography = document.getElementById('biography');
    const description = document.getElementById('description');

    if (typeof quill !== 'undefined' && quill) {
        quill.on('text-change', () => {
            if (biography) biography.value = quill.root.innerHTML;
            if (description) description.value = quill.root.innerHTML;
        });
    }
}

function initTomSelect() {
    const select = document.querySelector('#user-select');

    if (select) {
        new TomSelect('#user-select', {
            valueField: 'value',
            labelField: 'text',
            searchField: 'text',
            placeholder: 'Pesquisar estudante',
            preload: false,
            load: (query, callback) => {
                if (!query.length) return callback();
                axios.get('/dashboard/users/ajax?q=' + encodeURIComponent(query))
                    .then(response => callback(response.data))
                    .catch(() => callback());
            }
        });
    }
}

function initDateMasks() {
    const maskOptions = {
        mask: Date,
        pattern: 'd{/}`m{/}`Y',
        lazy: true,
        blocks: {
            d: { mask: IMask.MaskedRange, from: 1, to: 31, maxLength: 2 },
            m: { mask: IMask.MaskedRange, from: 1, to: 12, maxLength: 2 },
            Y: { mask: IMask.MaskedRange, from: 1900, to: 2099 }
        },
        format: (date) => {
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            return [day, month, year].join('/');
        },
        parse: (str) => {
            const [day, month, year] = str.split('/');
            return new Date(year, month - 1, day);
        }
    };

    document.querySelectorAll('.date').forEach(el => IMask(el, maskOptions));
}

function initDropzoneUploader() {
    Dropzone.autoDiscover = false;

    const dropzoneElement = document.querySelector('#dropzone');
    const attachmentsInput = document.querySelector('#attachments');

    if (!dropzoneElement || !attachmentsInput) return;

    let uploadedFiles = JSON.parse(attachmentsInput.value || '[]');
    if (!Array.isArray(uploadedFiles)) {
        uploadedFiles = [];
    }

    new Dropzone("#dropzone", {
        url: "/dashboard/lessons/attachments/upload",
        paramName: "file",
        maxFilesize: 100,
        dictDefaultMessage: "📁 Clique para selecionar ou solte os arquivos aqui",
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        success: (file, response) => {
            uploadedFiles.push(response);
            attachmentsInput.value = JSON.stringify(uploadedFiles);
        },
        error: (file, errorMessage) => {
            console.error("Upload failed:", errorMessage);
        }
    });
}

function initDeleteAttachmentButtons() {
    const attachmentsInput = document.querySelector('#attachments');
    if (!attachmentsInput) return;

    let uploadedFiles = JSON.parse(attachmentsInput.value || '[]');
    if (!Array.isArray(uploadedFiles)) {
        uploadedFiles = [];
    }

    document.addEventListener('click', (event) => {
        if (event.target.closest('.delete-btn')) {
            const card = event.target.closest('.attachment-card');
            const path = card?.dataset?.path;
            if (!path) return;

            fetch("/dashboard/lessons/attachments/delete", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ path })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'deleted') {
                        card.remove();
                        uploadedFiles = uploadedFiles.filter(file => file.path !== path);
                        attachmentsInput.value = JSON.stringify(uploadedFiles);
                    }
                })
                .catch(err => {
                    console.error('Failed to delete file:', err);
                });
        }
    });
}

function initDoubtFormSubmissions() {
    const form = document.getElementById('doubtForm');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            const url = form.action;

            axios.post(url, formData)
                .then(response => {
                    if (response.data.status === 'success') {
                        form.reset();

                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalPergunta'));
                        modal.hide();

                        const qaList = document.querySelector('.qa-list');

                        const newDoubt = response.data.doubt;

                        const html = `
                            <div class="qa-item">
                                <p class="qa-meta">
                                    <i class="bi bi-person-fill"></i> ${newDoubt.user.name} &nbsp;
                                    <i class="bi bi-calendar-event"></i> ${newDoubt.created_at_formatted}
                                </p>
                                <p class="student-question">
                                    <i class="bi bi-chat-left-text"></i> ${newDoubt.doubt}
                                </p>
                            </div>
                        `;

                        qaList.insertAdjacentHTML('beforeend', html);

                        Swal.fire({
                            icon: 'success',
                            title: 'Dúvida enviada!',
                            text: 'Sua dúvida foi registrada com sucesso.',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.data.message || 'Ocorreu um erro ao enviar sua dúvida.',
                        });
                    }
                })
                .catch(error => {
                    let errorMessage = 'Erro inesperado.';

                    if (error.response && error.response.data && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        errorMessage = Object.values(errors).flat().join('\n');
                    } else if (error.response?.data?.message) {
                        errorMessage = error.response.data.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao enviar',
                        text: errorMessage,
                    });
                });
        });
    }
}

/**
 * Initializes Plyr player and sets up topic click behavior.
 */
function initPlyrWithTopics() {
    const videoElement = document.querySelector('.js-player');
    if (!videoElement) return;

    const player = new Plyr(videoElement);
    let playTimer = null;
    const sentHistory = new Set();

    const topicItems = document.querySelectorAll('.topic-item[data-video]');
    if (!topicItems.length) return;

    function updateProgressBar() {
        const total = topicItems.length;
        const watched = document.querySelectorAll('.topic-item.watched').length;
        const percent = Math.round((watched / total) * 100);

        const progressBar = document.querySelector('.course-progress-bar .progress-bar');
        const progressText = document.querySelector('.course-progress-bar small');

        if (progressBar) {
            progressBar.style.width = percent + '%';
            progressBar.setAttribute('aria-valuenow', percent);
        }
        if (progressText) {
            progressText.textContent = `${percent}% concluído`;
        }
    }

    function sendPlayEvent(topicId, itemElement) {
        if (sentHistory.has(topicId)) return;

        itemElement.classList.add('watched');

        let badge = itemElement.querySelector('.badge-watched');
        if (!badge) {
            badge = document.createElement('span');
            badge.classList.add('badge', 'badge-watched');
            badge.textContent = 'Assistido';

            const topicInfo = itemElement.querySelector('.topic-info > div.d-flex');
            if (topicInfo) {
                topicInfo.appendChild(badge);
            }
        }

        updateProgressBar();

        axios.post(window.location.pathname + '/history', { topic_id: topicId })
            .then(() => {
                console.log('Histórico salvo com sucesso para o tópico ' + topicId);
                sentHistory.add(topicId);
            })
            .catch((error) => {
                console.error('Erro ao salvar histórico:', error);
            });
    }

    const firstItem = topicItems[0];
    if (firstItem) {
        firstItem.classList.add('active');
        const firstVideoUrl = firstItem.dataset.video;
        if (firstVideoUrl) {
            player.source = {
                type: 'video',
                sources: [{ src: firstVideoUrl, type: 'video/mp4' }],
            };

            player.once('playing', () => {
                topicItems.forEach(el => el.classList.remove('playing'));
                firstItem.classList.add('playing');
            });
        }
    }

    topicItems.forEach(item => {
        item.addEventListener('click', () => {
            const videoUrl = item.dataset.video;
            if (!videoUrl) return;

            topicItems.forEach(el => {
                el.classList.remove('active', 'playing');
                const icon = el.querySelector('.play-indicator');
                if (icon) icon.style.display = 'none';
            });

            item.classList.add('active');

            const currentIcon = item.querySelector('.play-indicator');
            if (currentIcon) currentIcon.style.display = 'inline';

            player.source = {
                type: 'video',
                sources: [{ src: videoUrl, type: 'video/mp4' }],
            };

            player.play();
        });
    });

    let sentEvent = false;

    player.on('timeupdate', () => {
        const currentItem = document.querySelector('.topic-item.active');
        const topicId = currentItem?.dataset.topicId;

        const mustWatchPercentage = 0.1;

        const duration = player.duration;
        const currentTime = player.currentTime;

        if (!sentEvent && duration && currentTime >= duration * mustWatchPercentage) {
            sentEvent = true;

            topicItems.forEach(el => el.classList.remove('playing'));

            currentItem?.classList.add('playing');

            sendPlayEvent(topicId, currentItem);
        }
    });

    player.on('loadeddata', () => {
        sentEvent = false;
    });

    updateProgressBar();

    player.on('playing', () => {
        topicItems.forEach(el => el.classList.remove('playing'));
        const currentItem = document.querySelector('.topic-item.active');
        currentItem?.classList.add('playing');
    });

    player.on('pause', () => {
        const currentItem = document.querySelector('.topic-item.active');
        currentItem?.classList.remove('playing');
    });

    player.on('ended', () => {
        const currentItem = document.querySelector('.topic-item.active');
        currentItem?.classList.remove('playing');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const quizModalEl = document.getElementById('quizModal');
    if (!quizModalEl) return;

    const questionCounterEl = document.getElementById('questionCounter');
    const progressBarEl = document.getElementById('quizProgressBar');
    const quizTopicEl = document.getElementById('quizTopic');
    const questionTextEl = document.getElementById('questionText');
    const answerOptionsContainerEl = document.getElementById('answerOptionsContainer');
    const btnSubmitAnswerEl = document.getElementById('btnSubmitAnswer');
    const btnNextQuestionEl = document.getElementById('btnNextQuestion');
    const btnNextQuestionTextEl = btnNextQuestionEl.querySelector('.btn-text');

    const quizFeedbackMessageEl = document.getElementById('quizFeedbackMessage');
    const topicFailedVideoInfoEl = document.getElementById('topicFailedVideoInfo');
    const videoTitleEl = document.getElementById('videoTitle');
    const videoThumbnailEl = document.getElementById('videoThumbnail');
    const videoLinkEl = document.getElementById('videoLink');

    // State variables
    let currentLessonId = null;
    let currentQuestionId = null;
    let selectedAnswerKey = null;
    let submittedAnswerButtonElement = null;

    // Axios is globally configured

    function showFeedback(message, type = 'info') {
        quizFeedbackMessageEl.textContent = message;
        quizFeedbackMessageEl.className = `quiz-feedback-message mb-3 text-center alert alert-${type}`;
        quizFeedbackMessageEl.style.display = 'block';
    }

    function hideFeedback() {
        quizFeedbackMessageEl.style.display = 'none';
        quizFeedbackMessageEl.textContent = '';
    }

    function resetUIForNewQuestion() {
        hideFeedback();
        answerOptionsContainerEl.innerHTML = '';
        selectedAnswerKey = null;
        submittedAnswerButtonElement = null;
        topicFailedVideoInfoEl.style.display = 'none';
        document.querySelectorAll('.btn-answer.active').forEach(btn => {
            btn.classList.remove('active');
            btn.disabled = false;
        });

        btnSubmitAnswerEl.style.display = 'inline-block';
        btnSubmitAnswerEl.disabled = true;

        btnNextQuestionEl.style.display = 'none';
        btnNextQuestionEl.disabled = true;
    }

    async function fetchNextQuestion() {
        if (!currentLessonId || currentLessonId === 'UNKNOWN_LESSON') {
            showFeedback('Erro: ID da aula não configurado para iniciar a avaliação.', 'danger');
            const modal = bootstrap.Modal.getInstance(quizModalEl);
            if (modal) modal.hide();
            return;
        }
        resetUIForNewQuestion();
        questionTextEl.textContent = 'Carregando próxima pergunta...';
        quizTopicEl.textContent = 'Carregando Tópico...';

        try {
            const response = await axios.get(`/aula/${currentLessonId}/quiz/next-question`);
            processQuizResponse(response.data);
        } catch (error) {
            console.error('Error fetching next question:', error);
            const errorMessage = error.response?.data?.message || error.message || 'Erro desconhecido.';
            showFeedback(`Erro ao carregar pergunta: ${errorMessage}`, 'danger');
            questionTextEl.textContent = 'Não foi possível carregar a pergunta.';
        }
    }

    function renderQuestion(data) {
        currentQuestionId = data.question.id;
        questionTextEl.textContent = data.question.text;
        quizTopicEl.textContent = `Tópico: ${data.topic_title}`;

        if (data.overall_current_question_number && data.total_questions_in_lesson) {
            questionCounterEl.textContent = `Questão ${data.overall_current_question_number} de ${data.total_questions_in_lesson}`;
        } else {
            questionCounterEl.textContent = `Questão N/A de N/A`;
            console.warn("Backend did not provide overall question count data.");
        }

        if (typeof data.overall_progress_in_lesson !== 'undefined') {
            progressBarEl.style.width = `${data.overall_progress_in_lesson}%`;
            progressBarEl.setAttribute('aria-valuenow', data.overall_progress_in_lesson);
        } else {
            progressBarEl.style.width = `0%`;
            progressBarEl.setAttribute('aria-valuenow', 0);
        }

        const optionsArray = data.question.options;
        answerOptionsContainerEl.innerHTML = '';

        optionsArray.forEach(optionObject => {
            const key = Object.keys(optionObject)[0];
            const optionText = optionObject[key];
            const button = document.createElement('button');
            button.classList.add('btn-answer');
            button.dataset.optionKey = key;
            const span = document.createElement('span');
            span.classList.add('option-label');
            span.textContent = key;
            button.appendChild(span);
            button.append(` ${optionText}`);
            button.addEventListener('click', (event) => handleAnswerSelection(event, key));
            answerOptionsContainerEl.appendChild(button);
        });
    }

    function handleAnswerSelection(event, optionKey) {
        const previouslySelected = answerOptionsContainerEl.querySelector('.btn-answer.active');
        if (previouslySelected) {
            previouslySelected.classList.remove('active');
        }
        submittedAnswerButtonElement = event.currentTarget;
        submittedAnswerButtonElement.classList.add('active');
        selectedAnswerKey = optionKey;
        btnSubmitAnswerEl.disabled = false;
    }

    async function submitAnswerHandler() {
        if (!selectedAnswerKey || !currentQuestionId || !currentLessonId) {
            showFeedback('Por favor, selecione uma resposta.', 'warning');
            return;
        }
        btnSubmitAnswerEl.disabled = true;

        try {
            const response = await axios.post(`/aula/${currentLessonId}/quiz/submit-answer`, {
                question_id: currentQuestionId,
                selected_option: selectedAnswerKey
            });
            processQuizResponse(response.data);
        } catch (error) {
            console.error('Error submitting answer:', error);
            const errorMessage = error.response?.data?.message || error.message || 'Erro desconhecido.';
            showFeedback(`Erro ao enviar resposta: ${errorMessage}`, 'danger');
            btnSubmitAnswerEl.disabled = false;
        }
    }

    function processQuizResponse(data) {
        hideFeedback();
        console.log('Backend Response:', data);

        if (data.status === 'question') {
            renderQuestion(data);
        } else if (data.status === 'answer_received' || data.status === 'next_topic_ready' || data.status === 'topic_failed') {
            answerOptionsContainerEl.querySelectorAll('.btn-answer').forEach(btn => {
                btn.disabled = true;
            });

            btnSubmitAnswerEl.style.display = 'none';
            btnNextQuestionEl.style.display = 'inline-block';

            if (data.is_correct) {
                showFeedback(data.message || 'Resposta Correta!', 'success');
            } else {
                if (submittedAnswerButtonElement) {
                    submittedAnswerButtonElement.classList.remove('active');
                }
                showFeedback(data.message || 'Resposta Incorreta!', 'danger');
            }

            if (data.status !== 'topic_failed') {
                btnNextQuestionEl.disabled = false;
            } else {
                if (data.video) {
                    topicFailedVideoInfoEl.style.display = 'block';
                    videoTitleEl.textContent = data.video.name || 'Vídeo de revisão do tópico.';
                    if (data.video.thumbnail_path) {
                        videoThumbnailEl.src = data.video.thumbnail_path;
                        videoThumbnailEl.style.display = 'block';
                    } else { videoThumbnailEl.style.display = 'none'; }
                    if (data.video.path) {
                        videoLinkEl.href = '#!';
                        videoLinkEl.textContent = `Revisar o vídeo: ${data.video.name}`;
                        videoLinkEl.style.display = 'block';
                    } else { videoLinkEl.style.display = 'none'; }
                }
                btnNextQuestionEl.disabled = true;
            }

        } else if (data.status === 'finished') {
            questionTextEl.textContent = data.message || 'Quiz finalizado com sucesso!';
            quizTopicEl.textContent = 'Parabéns!';
            answerOptionsContainerEl.innerHTML = '';

            if (data.total_questions_in_lesson) {
                progressBarEl.style.width = '100%';
                questionCounterEl.textContent = `Completado ${data.total_questions_in_lesson} de ${data.total_questions_in_lesson} questões!`;
            } else {
                progressBarEl.style.width = '100%';
                questionCounterEl.textContent = 'Completo!';
            }

            btnSubmitAnswerEl.style.display = 'none';
            btnNextQuestionTextEl.textContent = 'Fechar Quiz';
            btnNextQuestionEl.style.display = 'inline-block';
            btnNextQuestionEl.disabled = false;
            btnNextQuestionEl.onclick = () => {
                const modal = bootstrap.Modal.getInstance(quizModalEl);
                modal.hide();
            };
            showFeedback(data.message, 'success');
        } else if (data.status === 'error') {
            showFeedback(data.message, 'danger');
            btnSubmitAnswerEl.disabled = false;
        }
    }

    quizModalEl.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        if (button && button.dataset.lessonId && button.dataset.lessonId !== 'UNKNOWN_LESSON') {
            currentLessonId = button.dataset.lessonId;
            btnNextQuestionTextEl.textContent = 'Próxima';
            btnNextQuestionEl.onclick = fetchNextQuestion;
            fetchNextQuestion();
        } else {
            console.error('Lesson ID not found or invalid on triggering button for quizModal.');
            showFeedback('Erro: ID da aula não disponível para iniciar a avaliação.', 'danger');
            event.preventDefault();
        }
    });

    quizModalEl.addEventListener('hidden.bs.modal', () => {
        resetUIForNewQuestion();
        questionTextEl.textContent = 'Carregando Pergunta...';
        quizTopicEl.textContent = 'Carregando Tópico...';
        questionCounterEl.textContent = 'Questão ... de ...';
        progressBarEl.style.width = '0%';
        currentQuestionId = null;

        btnNextQuestionTextEl.textContent = 'Próxima';
        btnNextQuestionEl.onclick = fetchNextQuestion;
    });

    btnSubmitAnswerEl.addEventListener('click', submitAnswerHandler);
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('avaliacao-form');
    const statusEl = document.getElementById('avaliacao-status');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const lessonId = form.dataset.lessonId;
            const formData = new FormData(form);

            try {
                const response = await axios.post(`/aula/${lessonId}/feedback`, formData);
                statusEl.innerHTML = `<div class="alert alert-success text-center">${response.data.message}</div>`;
                form.style.display = 'none';
            } catch (error) {
                if (error.response?.status === 422) {
                    const errors = Object.values(error.response.data.errors).flat().join('<br>');
                    statusEl.innerHTML = `<div class="alert alert-danger">${errors}</div>`;
                } else {
                    statusEl.innerHTML = `<div class="alert alert-danger">Erro inesperado ao enviar a avaliação.</div>`;
                }
            }
        });
    }
});