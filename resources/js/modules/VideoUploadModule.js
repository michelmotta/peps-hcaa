import Swal from 'sweetalert2';

export const VideoUploadModule = {
    init() {
        const fileInput = document.getElementById('file');
        if (!fileInput) {
            return;
        }

        const videoUploadWrapper = document.getElementById('video-upload-wrapper');
        const loadingContainer = document.getElementById('loading-container');
        const uploadStatus = document.getElementById('upload-status');
        const videoIdInput = document.getElementById('video-id-input');
        const videoPreviewContainer = document.getElementById('video-preview-container');
        const videoThumbnailPreview = document.getElementById('video-thumbnail-preview');
        const removeVideoBtn = document.getElementById('remove-video-btn');
        const videoFileName = document.getElementById('video-file-name');
        const videoFileSize = document.getElementById('video-file-size');

        function resetUploader() {
            if (uploadStatus) uploadStatus.innerHTML = '';
            if (videoPreviewContainer) videoPreviewContainer.style.display = 'none';
            if (loadingContainer) loadingContainer.style.display = 'none';
            videoIdInput.value = '';
            fileInput.value = '';
            fileInput.disabled = false;
            if (videoUploadWrapper) videoUploadWrapper.style.display = 'block';
        }

        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            const uploadUrl = this.dataset.uploadUrl;
            if (!file || !uploadUrl) return;

            fileInput.classList.remove('is-invalid');
            if (uploadStatus) uploadStatus.innerHTML = '';

            const maxSize = 300 * 1024 * 1024;
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Arquivo muito grande!',
                    text: 'São permitidos vídeos com no máximo 300MB.'
                });
                this.value = '';
                return;
            }

            if (file.type !== 'video/mp4') {
                Swal.fire({
                    icon: 'error',
                    title: 'Formato inválido!',
                    text: 'São permitidos apenas arquivos MP4.'
                });
                this.value = '';
                return;
            }

            resetUploader();

            if (videoFileName) videoFileName.textContent = file.name;
            if (videoFileSize) videoFileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

            const formData = new FormData();
            formData.append('file', file);

            if (loadingContainer) loadingContainer.style.display = 'block';
            this.disabled = true;

            axios.post(uploadUrl, formData)
                .then(response => {
                    if (response.data.success) {
                        videoIdInput.value = response.data.video_id;
                        if (videoThumbnailPreview && response.data.thumbnail_url) {
                            videoThumbnailPreview.src = response.data.thumbnail_url;
                        }
                        if (removeVideoBtn) {
                            const baseUrl = removeVideoBtn.getAttribute('data-base-url');
                            const deleteUrl = `${baseUrl}/${response.data.video_id}`;
                            removeVideoBtn.setAttribute('data-delete-url', deleteUrl);
                        }
                        if (videoUploadWrapper) videoUploadWrapper.style.display = 'none';
                        if (videoPreviewContainer) videoPreviewContainer.style.display = 'block';
                        if (loadingContainer) loadingContainer.style.display = 'none'; // Updated
                        this.disabled = false;

                        Swal.fire({
                            icon: 'success',
                            title: 'Upload concluído!',
                            text: 'Seu vídeo foi enviado com sucesso.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(response.data.message || 'Erro no servidor');
                    }
                })
                .catch(error => {
                    const message = error.response?.data?.errors?.file?.[0]
                        || error.response?.data?.message
                        || 'Falha no envio do vídeo.';

                    Swal.fire({
                        icon: 'error',
                        title: 'Falha no envio',
                        text: message
                    });

                    fileInput.classList.add('is-invalid');
                    resetUploader();
                });
        });

        if (removeVideoBtn) {
            removeVideoBtn.addEventListener('click', async function () {
                const deleteUrl = this.getAttribute('data-delete-url');
                if (!deleteUrl) {
                    console.error('Delete URL not found.');
                    return;
                }

                const userConfirmed = await Swal.fire({
                    title: 'Quer mesmo trocar o vídeo?',
                    text: "Esta ação removerá o vídeo permanentemente.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sim, remover!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => result.isConfirmed);

                if (!userConfirmed) {
                    return;
                }

                axios.delete(deleteUrl)
                    .then(response => {
                        if (response.data.success) {
                            resetUploader();
                            Swal.fire({
                                icon: 'info',
                                title: 'Vídeo removido',
                                text: response.data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            throw new Error(response.data.message || 'Não foi possível remover.');
                        }
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Erro ao remover o vídeo.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: message
                        });
                    });
            });
        }
    }
};