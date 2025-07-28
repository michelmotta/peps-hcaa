import Swal from 'sweetalert2';

export const VideoUploadModule = {
    init() {
        const fileInput = document.getElementById('file');

        if (!fileInput) {
            return;
        }

        const videoUploadWrapper = document.getElementById('video-upload-wrapper');
        const progressBar = document.getElementById('progress-bar');
        const progressContainer = document.getElementById('progress-container');
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
            if (progressContainer) progressContainer.style.display = 'none';
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.textContent = '0%';
                progressBar.classList.remove('bg-success');
                progressBar.classList.add('progress-bar-animated');
            }
            videoIdInput.value = '';
            fileInput.value = '';
            fileInput.disabled = false;
            if (videoUploadWrapper) videoUploadWrapper.style.display = 'block';
        }

        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            const uploadUrl = this.dataset.uploadUrl;
            if (!file || !uploadUrl) return;

            if (videoFileName) videoFileName.textContent = file.name;
            if (videoFileSize) videoFileSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

            const formData = new FormData();
            formData.append('file', file);

            resetUploader();
            if (progressContainer) progressContainer.style.display = 'block';
            this.disabled = true;

            const config = {
                onUploadProgress: function (progressEvent) {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    if (progressBar) {
                        progressBar.style.width = percentCompleted + '%';
                        progressBar.textContent = `Enviando... ${percentCompleted}%`;
                    }
                }
            };

            axios.post(uploadUrl, formData, config)
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
                        if (progressContainer) progressContainer.style.display = 'none';
                        this.disabled = false;
                    } else {
                        throw new Error(response.data.message || 'Server error during upload.');
                    }
                })
                .catch(error => {
                    const message = error.response?.data?.errors?.file?.[0] || error.response?.data?.message || 'Falha no envio do vídeo.';
                    if (uploadStatus) uploadStatus.innerHTML = `<div class="alert alert-danger">${message}</div>`;
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
                            if (uploadStatus) uploadStatus.innerHTML = `<div class="alert alert-info">${response.data.message}</div>`;
                        } else {
                            throw new Error(response.data.message || 'Could not delete video.');
                        }
                    })
                    .catch(error => {
                        const message = error.response?.data?.message || 'Erro ao remover o vídeo.';
                        if (uploadStatus) uploadStatus.innerHTML = `<div class="alert alert-danger">${message}</div>`;
                    });
            });
        }
    }
};
