import { AlertManager } from './AlertManager';

export class FormModule {
    static init() {
        this.initDoubtFormSubmissions();
    }
    
    static initDoubtFormSubmissions() {
        const form = document.getElementById('doubtForm');
        if (!form) return;
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const url = form.action;
            
            try {
                const response = await axios.post(url, formData);
                
                if (response.data.status === 'success') {
                    this.handleDoubtSuccess(form, response.data.doubt);
                } else {
                    AlertManager.showError(response.data.message || 'Ocorreu um erro ao enviar sua dúvida.');
                }
            } catch (error) {
                this.handleDoubtError(error);
            }
        });
    }
    
    static handleDoubtSuccess(form, newDoubt) {
        form.reset();
        
        // Close collapse if exists
        const collapseEl = document.getElementById('collapseAskQuestion');
        if (collapseEl) {
            const collapse = bootstrap.Collapse.getInstance(collapseEl);
            collapse?.hide();
        }
        
        this.addDoubtToChat(newDoubt);
        AlertManager.showSuccess('Sua dúvida foi registrada com sucesso.');
    }
    
    static addDoubtToChat(doubt) {
        const chatFeed = document.querySelector('.chat-feed');
        const emptyMessage = document.getElementById('empty-chat-message');
        
        if (emptyMessage) {
            emptyMessage.remove();
        }
        
        const html = `
            <div class="chat-message student-message">
                <div class="chat-avatar">
                    <img src="${doubt.user.file_path}" alt="${doubt.user.name}">
                </div>
                <div class="message-content">
                    <div class="chat-bubble">
                        <p>${doubt.doubt}</p>
                    </div>
                    <div class="chat-meta">
                        <strong>${doubt.user.name}</strong> &middot; ${doubt.created_at_formatted}
                    </div>
                </div>
            </div>
        `;
        
        chatFeed.insertAdjacentHTML('beforeend', html);
    }
    
    static handleDoubtError(error) {
        let errorMessage = 'Erro inesperado.';
        
        if (error.response?.data?.errors) {
            errorMessage = Object.values(error.response.data.errors).flat().join('\n');
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }
        
        AlertManager.showError(errorMessage);
    }
}