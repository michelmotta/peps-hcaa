import { Dropzone } from "dropzone";
import "dropzone/dist/dropzone.css";

export class FileUploadModule {
    static init() {
        this.initDropzoneUploader();
        this.initDeleteAttachmentButtons();
    }
    
    static initDropzoneUploader() {
        Dropzone.autoDiscover = false;
        
        const dropzoneElement = document.querySelector('#dropzone');
        const attachmentsInput = document.querySelector('#attachments');
        
        if (!dropzoneElement || !attachmentsInput) return;
        
        let uploadedFiles = this.parseUploadedFiles(attachmentsInput.value);
        
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
    
    static initDeleteAttachmentButtons() {
        const attachmentsInput = document.querySelector('#attachments');
        if (!attachmentsInput) return;
        
        let uploadedFiles = this.parseUploadedFiles(attachmentsInput.value);
        
        document.addEventListener('click', (event) => {
            if (!event.target.closest('.delete-btn')) return;
            
            const card = event.target.closest('.attachment-card');
            const path = card?.dataset?.path;
            if (!path) return;
            
            this.deleteAttachment(path, card, uploadedFiles, attachmentsInput);
        });
    }
    
    static parseUploadedFiles(value) {
        try {
            const parsed = JSON.parse(value || '[]');
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    }
    
    static async deleteAttachment(path, card, uploadedFiles, attachmentsInput) {
        try {
            const response = await fetch("/dashboard/lessons/attachments/delete", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ path })
            });
            
            const data = await response.json();
            
            if (data.status === 'deleted') {
                card.remove();
                uploadedFiles = uploadedFiles.filter(file => file.path !== path);
                attachmentsInput.value = JSON.stringify(uploadedFiles);
            }
        } catch (err) {
            console.error('Failed to delete file:', err);
        }
    }
}