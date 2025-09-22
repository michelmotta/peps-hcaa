export class FeedbackModule {
    static init() {
        const form = document.getElementById('feedback-form');
        if (form) {
            new FeedbackForm(form);
        }
    }
}

class FeedbackForm {
    constructor(form) {
        this.form = form;
        this.container = document.querySelector('.feedback-body');
        this.statusEl = document.getElementById('feedback-status');
        this.button = form.querySelector('.submit-button');
        this.buttonText = this.button?.querySelector('span');
        this.buttonIcon = this.button?.querySelector('i');
        this.init();
    }

    init() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    async handleSubmit(e) {
        e.preventDefault();
        this.setLoadingState(true);
        const lessonId = this.form.dataset.lessonId;
        const formData = new FormData(this.form);

        try {
            const response = await axios.post(`/aula/${lessonId}/feedback`, formData);
            this.handleSuccess(response.data.feedback);
        } catch (error) {
            this.handleError(error);
        } finally {
            this.setLoadingState(false);
        }
    }

    setLoadingState(isLoading) {
        if (!this.button) return;
        this.button.disabled = isLoading;
        this.button.classList.toggle('is-loading', isLoading);

        if (this.buttonText) {
            this.buttonText.textContent = isLoading ? 'Enviando...' : 'Enviar Avaliação';
        }

        if (this.buttonIcon) {
            this.buttonIcon.className = isLoading
                ? 'spinner-border spinner-border-sm me-1'
                : 'bi bi-send-fill';
        }

        if (this.statusEl) {
            this.statusEl.innerHTML = '';
            this.statusEl.style.display = 'none';
        }
    }

    handleSuccess(feedbackData) {
        if (!this.container || !feedbackData) {
            console.error('Feedback container or data is missing.');
            return;
        }

        const buildStars = (rating) => {
            let starsHtml = '';
            for (let i = 1; i <= 5; i++) {
                const isFilled = i <= rating ? 'is-filled' : '';
                starsHtml += `<i class="bi bi-star-fill star-display ${isFilled}"></i>`;
            }
            return starsHtml;
        };

        const commentHtml = feedbackData.comentario
            ? `<div class="summary-item">
                   <h4 class="item-title">Seu Comentário</h4>
                   <blockquote class="comment-display">${feedbackData.comentario}</blockquote>
               </div>`
            : '';

        const summaryHtml = `
            <div class="feedback-summary">
                <div class="summary-header">
                    <i class="bi bi-check-circle-fill"></i>
                    <h3>Sua avaliação foi registrada. Obrigado!</h3>
                </div>
                <div class="summary-content">
                    <div class="summary-item">
                        <h4 class="item-title">Sua Avaliação</h4>
                        <div class="stars-display">
                            ${buildStars(feedbackData.rating)}
                        </div>
                    </div>
                    ${commentHtml}
                </div>
            </div>`;

        this.container.innerHTML = summaryHtml;
    }

    handleError(error) {
        let errorMessage = 'Ocorreu um erro inesperado. Tente novamente.';

        if (error.response?.status === 422) {
            errorMessage = Object.values(error.response.data.errors).flat().join('<br>');
        }

        if (this.statusEl) {
            this.statusEl.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
            this.statusEl.style.display = 'block';
            this.form.prepend(this.statusEl);
        }
    }
}