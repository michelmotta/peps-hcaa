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

        const myDropzone = new Dropzone("#dropzone", {
            url: "/dashboard/lessons/attachments/upload",
            paramName: "file",
            maxFilesize: 100,
            dictDefaultMessage: "📁 Clique para selecionar ou solte os arquivos aqui",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            success: (file, response) => {
                const currentFiles = FileUploadModule.parseUploadedFiles(attachmentsInput.value);
                currentFiles.push(response);
                attachmentsInput.value = JSON.stringify(currentFiles);

                FileUploadModule.addFileToTable(response);

                setTimeout(() => myDropzone.removeFile(file), 500);
            },
            error: (file, errorMessage) => {
                console.error("Upload failed:", errorMessage);
                setTimeout(() => myDropzone.removeFile(file), 2000);
            }
        });
    }

    static addFileToTable(fileData) {
        const tableBody = document.querySelector('table tbody');
        if (!tableBody) return;

        const noAttachmentsRow = document.getElementById('no-attachments-row');
        if (noAttachmentsRow) noAttachmentsRow.remove();

        const getIconClass = (ext) => {
            ext = ext.toLowerCase();
            if (['png', 'jpg', 'jpeg', 'gif'].includes(ext)) return 'bi-file-earmark-image';
            if (['mp4', 'mov', 'avi'].includes(ext)) return 'bi-file-earmark-play';
            if (ext === 'pdf') return 'bi-filetype-pdf';
            return 'bi-file-earmark-text';
        };

        const iconClass = getIconClass(fileData.extension);
        const fileSizeMB = (fileData.size / 1024 / 1024).toFixed(2) + ' MB';
        const fileUrl = `/storage/${fileData.path}`;

        const newRow = `
            <tr class="attachment-card" data-path="${fileData.path}">
                <td><i class="${iconClass} fs-4 text-primary"></i></td>
                <td class="fw-semibold text-dark">${fileData.name}</td>
                <td class="text-muted">${fileSizeMB}</td>
                <td class="text-end">
                    <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="tooltip" title="Visualizar">
                        <i class="bi bi-eye"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-bs-toggle="tooltip" title="Remover">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', newRow);
    }

    static initDeleteAttachmentButtons() {
        const attachmentsInput = document.querySelector('#attachments');
        if (!attachmentsInput) return;

        document.addEventListener('click', async (event) => {
            const deleteButton = event.target.closest('.delete-btn');
            if (!deleteButton) return;

            event.preventDefault();
            const card = deleteButton.closest('.attachment-card');
            const path = card?.dataset?.path;
            if (!path) return;

            try {
                const response = await fetch("/dashboard/lessons/attachments/delete", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ path })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Failed to delete file:', errorData.message || 'Unknown server error');
                    return;
                }

                card.remove();

                const currentFiles = FileUploadModule.parseUploadedFiles(attachmentsInput.value);
                const newFiles = currentFiles.filter(file => file.path !== path);
                attachmentsInput.value = JSON.stringify(newFiles);

            } catch (err) {
                console.error('Failed to delete file due to a network error:', err);
            }
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
}
