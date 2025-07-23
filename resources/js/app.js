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
import { initQuizPlayer } from './quiz-player';

import Plyr from 'plyr';
import 'plyr/dist/plyr.css';

import Tagify from '@yaireo/tagify';
import '@yaireo/tagify/dist/tagify.css';

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
    initTagfy();
    initQuillSync();
    initTomSelect();
    initDateMasks();
    initDropzoneUploader();
    initDeleteAttachmentButtons();
    initDoubtFormSubmissions();
    initPlyrWithTopics();
    initQuizJson();
    initQuizPlayer();
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

function initTagfy() {
    const tagifyInput = document.querySelector('#subspecialties');
    if (tagifyInput) {
        new Tagify(tagifyInput);
    }

    const fileInput = document.getElementById('file');
    const imagePreview = document.getElementById('image-preview');

    if (fileInput && imagePreview) {
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
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
    const userSelect = document.querySelector('#user-select');
    const professorSelect = document.querySelector('#professor-select');
    const lessonSelect = document.querySelector('#lesson-select');

    const tomSelectPtBr = {
        render: {
            loading: function () {
                return '<div class="p-2 text-muted">Carregando...</div>';
            },

            no_results: function () {
                return '<div class="p-2 text-muted">Nenhum resultado encontrado.</div>';
            }
        }
    };

    if (userSelect) {
        new TomSelect('#user-select', {
            ...tomSelectPtBr,
            valueField: 'value',
            labelField: 'text',
            searchField: 'text',
            placeholder: 'Pesquisar pelo nome',
            preload: false,
            load: (query, callback) => {
                if (!query.length) return callback();
                axios.get('/dashboard/users/ajax?q=' + encodeURIComponent(query))
                    .then(response => callback(response.data))
                    .catch(() => callback());
            }
        });
    }

    if (professorSelect) {
        new TomSelect('#professor-select', {
            ...tomSelectPtBr,
            valueField: 'value',
            labelField: 'text',
            searchField: 'text',
            placeholder: 'Pesquisar pelo nome',
            preload: false,
            load: (query, callback) => {
                if (!query.length) return callback();
                axios.get('/dashboard/professors/ajax?q=' + encodeURIComponent(query))
                    .then(response => callback(response.data))
                    .catch(() => callback());
            }
        });
    }

    if (lessonSelect) {
        new TomSelect('#lesson-select', {
            ...tomSelectPtBr,
            valueField: 'value',
            labelField: 'text',
            searchField: 'text',
            placeholder: 'Pesquisar pelo nome da aula',
            preload: false,
            load: (query, callback) => {
                if (!query.length) return callback();
                axios.get('/dashboard/lessons/ajax?q=' + encodeURIComponent(query))
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

                        const collapseEl = document.getElementById('collapseAskQuestion');
                        if (collapseEl) {
                            const collapse = bootstrap.Collapse.getInstance(collapseEl);
                            collapse?.hide();
                        }

                        const chatFeed = document.querySelector('.chat-feed');
                        const newDoubt = response.data.doubt;

                        const emptyMessage = document.getElementById('empty-chat-message');
                        if (emptyMessage) {
                            emptyMessage.remove();
                        }

                        const html = `
                            <div class="chat-message student-message">
                                <div class="chat-avatar">
                                    <img src="${newDoubt.user.file_path}" alt="${newDoubt.user.name}">
                                </div>
                                <div class="message-content">
                                    <div class="chat-bubble">
                                        <p>${newDoubt.doubt}</p>
                                    </div>
                                    <div class="chat-meta">
                                        <strong>${newDoubt.user.name}</strong> &middot; ${newDoubt.created_at_formatted}
                                    </div>
                                </div>
                            </div>
                        `;

                        chatFeed.insertAdjacentHTML('beforeend', html);

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

        updateProgressBar();

        axios.post(window.location.pathname + '/history', { topic_id: topicId })
            .then(() => {
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
    const addButton = document.getElementById('add-subspecialty');
    const wrapper = document.getElementById('subspecialties-wrapper');

    if (addButton && wrapper) {
        addButton.addEventListener('click', () => {
            const div = document.createElement('div');
            div.classList.add('input-group', 'mb-2', 'subspecialty-item');
            div.innerHTML = `
                <input type="text" name="subspecialties[]" class="form-control" placeholder="Subespecialidade" required>
                <button class="btn btn-outline-danger remove-subspecialty" type="button">×</button>
            `;
            wrapper.appendChild(div);
        });

        wrapper.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-subspecialty')) {
                e.target.closest('.subspecialty-item').remove();
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('feedback-form');

    if (form) {
        const statusEl = document.getElementById('feedback-status');
        const button = form.querySelector('.submit-button');
        const buttonText = button.querySelector('span');
        const buttonIcon = button.querySelector('i');

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            button.disabled = true;
            button.classList.add('is-loading');
            buttonText.textContent = 'Enviando...';
            buttonIcon.className = 'spinner';
            statusEl.style.display = 'none';

            const lessonId = form.dataset.lessonId;
            const formData = new FormData(form);

            try {
                const response = await axios.post(`/aula/${lessonId}/feedback`, formData);

                statusEl.innerHTML = `<div class="alert alert-success">${response.data.message}</div>`;
                statusEl.style.display = 'block';
                form.style.display = 'none';

            } catch (error) {
                let errorMessage = 'Ocorreu um erro inesperado. Tente novamente.';
                if (error.response?.status === 422) {
                    errorMessage = Object.values(error.response.data.errors).flat().join('<br>');
                }
                statusEl.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
                statusEl.style.display = 'block';

            } finally {
                if (form.style.display !== 'none') {
                    button.disabled = false;
                    button.classList.remove('is-loading');
                    buttonText.textContent = 'Enviar Avaliação';
                    buttonIcon.className = 'bi bi-send-fill';
                }
            }
        });
    }
});