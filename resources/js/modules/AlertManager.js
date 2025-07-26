export class AlertManager {
    static showFeedbackAlert() {
        const alert = document.querySelector('.custom-alert');
        if (alert) {
            setTimeout(() => {
                alert.classList.add('alert-visible');
            }, 200);
        }
    }
    
    static showSuccess(message, timer = 3000) {
        return Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: message,
            timer,
            showConfirmButton: false
        });
    }
    
    static showError(message) {
        return Swal.fire({
            icon: 'error',
            title: 'Erro',
            text: message,
        });
    }
}