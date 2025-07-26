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
            this.handleSuccess(response.data.message);
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
            this.buttonIcon.className = isLoading ? 'spinner' : 'bi bi-send-fill';
        }
        
        if (this.statusEl) {
            this.statusEl.style.display = 'none';
        }
    }
    
    handleSuccess(message) {
        if (this.statusEl) {
            this.statusEl.innerHTML = `<div class="alert alert-success">${message}</div>`;
            this.statusEl.style.display = 'block';
        }
        this.form.style.display = 'none';
    }
    
    handleError(error) {
        let errorMessage = 'Ocorreu um erro inesperado. Tente novamente.';
        
        if (error.response?.status === 422) {
            errorMessage = Object.values(error.response.data.errors).flat().join('<br>');
        }
        
        if (this.statusEl) {
            this.statusEl.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
            this.statusEl.style.display = 'block';
        }
    }
}