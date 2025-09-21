@csrf
<section>
    <div class="card mb-5 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0 d-flex align-items-center">
                <i data-feather="clipboard" class="me-2"></i>
                Apresentação
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            placeholder="Digite o título" name="title"
                            value="@if(isset($topic)){{ $topic->title }}@else{{ old('title') }}@endif"
                            required>
                        @error('title')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mt-2 mb-4">
                        <label class="form-label">Resumo</label>
                        <textarea name="resume" id="resume" class="form-control">@if (isset($topic)){{ $topic->resume }}@else{{ old('resume') }}@endif</textarea>
                        @error('resume')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-5 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0 d-flex align-items-center">
                <i data-feather="book-open" class="me-2"></i>
                Conteúdo
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div id="upload-status" class="col-md-12 mb-3"></div>
                <div class="col-md-12" id="video-upload-wrapper"
                    @if (isset($topic) && $topic->video) style="display: none;" @endif>
                    <div class="mb-3">
                        <label for="file" class="form-label fw-bold">Vídeo</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                            name="file" accept="video/mp4" data-upload-url="{{ route('dashboard.videos.store') }}">
                        <small class="d-block text-muted">Formatos permitidos: MP4. Tamanho máximo: 300MB</small>
                        @error('file')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <input type="hidden" name="video_id" id="video-id-input" value="{{ $topic->video->id ?? '' }}">
                <div class="col-md-12 mb-3" id="video-preview-container"
                    @if (!isset($topic) || !$topic->video) style="display: none;" @endif>
                    <div class="card shadow-sm border rounded-3 overflow-hidden">
                        <div class="card-header bg-body-tertiary p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 id="video-file-name" class="card-title fw-bold text-dark mb-0 text-truncate">
                                    {{ $topic->video->name ?? 'nome_do_arquivo.mp4' }}
                                </h5>
                                <span
                                    class="badge bg-primary-subtle text-primary-emphasis rounded-pill fw-semibold fs-6">MP4</span>
                            </div>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="row g-3 g-md-4">
                                <div class="col-12 col-lg-3 d-flex align-items-center">
                                    <div class="position-relative rounded-2 overflow-hidden shadow-sm w-100"
                                        style="padding-top: 56.25%;">
                                        <img id="video-thumbnail-preview"
                                            src="{{ isset($topic) && $topic->video ? Storage::url($topic->video->thumbnail_path) : '' }}"
                                            alt="Thumbnail do vídeo" class="position-absolute top-0 start-0 w-100 h-100"
                                            style="object-fit: cover;">
                                        <div class="position-absolute top-50 start-50 translate-middle">
                                            <i class="bi bi-play-circle-fill text-white display-4"
                                                style="filter: drop-shadow(0 0 5px rgba(0,0,0,.7));"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-9">
                                    <h6 class="fw-bold text-muted mb-3">DETALHES DO ARQUIVO</h6>
                                    <div class="list-group list-group-flush">
                                        <div
                                            class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-hdd-stack fs-5 text-muted me-3"></i>
                                                <span class="fw-semibold">Tamanho</span>
                                            </div>
                                            <span id="video-file-size" class="fw-bold text-dark">
                                                {{ isset($topic) && $topic->video ? round($topic->video->size / (1024 * 1024), 2) . ' MB' : '' }}
                                            </span>
                                        </div>
                                        <div
                                            class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar-check fs-5 text-muted me-3"></i>
                                                <span class="fw-semibold">Data do Envio</span>
                                            </div>
                                            <span class="fw-bold text-dark">
                                                @if (isset($topic) && $topic->video && $topic->video->created_at)
                                                    {{ $topic->video->created_at->format('d/m/Y') }}
                                                @else
                                                    {{ date('d/m/Y') }}
                                                @endif
                                            </span>
                                        </div>
                                        <div
                                            class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-check-circle-fill fs-5 text-success me-3"></i>
                                                <span class="fw-semibold">Status</span>
                                            </div>
                                            <span class="fw-bold text-success">Carregado</span>
                                        </div>
                                    </div>
                                    <div class="text-end mt-4">
                                        <button type="button" id="remove-video-btn" class="btn btn-outline-danger"
                                            data-base-url="{{ url('dashboard/videos') }}"
                                            data-delete-url="{{ isset($topic) && $topic->video ? route('dashboard.videos.destroy', $topic->video->id) : '' }}">
                                            <i class="bi bi-trash me-1"></i>Trocar Vídeo
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3" id="loading-container" style="display: none;">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="spinner-border text-primary me-3" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                                <span class="fw-semibold text-dark">Enviando e processando o vídeo, por favor
                                    aguarde...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Conteúdo</label>
                        <div id="editor">
                            @if (isset($topic))
                                {!! $topic->description !!}@else{!! old('description') !!}
                            @endif
                        </div>
                        <input type="hidden" name="description" id="description"
                            value="@if (isset($topic)) {{ $topic->description }}@else{{ old('description') }} @endif">
                        @error('description')
                            <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-5 shadow-sm">
        <div class="card-header bg-body-tertiary d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i data-feather="paperclip" class="me-2"></i>
                Anexos
            </h4>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;">Tipo</th>
                            <th scope="col">Arquivo</th>
                            <th scope="col" style="width: 15%;">Tamanho</th>
                            <th scope="col" class="text-end" style="width: 20%;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($topic) && $topic->attachments->isNotEmpty())
                            @foreach ($topic->attachments as $file)
                                <tr class="attachment-card" data-path="{{ $file['path'] ?? '' }}">
                                    <td>
                                        @php
                                            $ext = strtolower($file['extension'] ?? '');
                                            $iconClass = 'bi-file-earmark-text';
                                            if (in_array($ext, ['png', 'jpg', 'jpeg'])) {
                                                $iconClass = 'bi-file-earmark-image';
                                            } elseif (in_array($ext, ['mp4', 'mov', 'avi'])) {
                                                $iconClass = 'bi-file-earmark-play';
                                            } elseif (in_array($ext, ['pdf'])) {
                                                $iconClass = 'bi-filetype-pdf';
                                            }
                                        @endphp
                                        <i class="{{ $iconClass }} fs-4 text-primary"></i>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        {{ $file['name'] ?? 'Arquivo' }}
                                    </td>
                                    <td class="text-muted">
                                        {{ isset($file['size']) ? round($file['size'] / 1024 / 1024, 2) . ' MB' : '' }}
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ Storage::url($file['path'] ?? '') }}" target="_blank"
                                            class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="tooltip"
                                            title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                            data-bs-toggle="tooltip" title="Remover">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr id="no-attachments-row">
                                <td colspan="4" class="text-center text-muted p-4">
                                    <i class="bi bi-folder-x fs-2"></i>
                                    <p class="mt-2 mb-0">Nenhum anexo salvo.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <hr class="my-4">

            <h6 class="fw-bold text-muted mb-3">ADICIONAR NOVOS ARQUIVOS</h6>
            <div id="dropzone" class="dropzone border-2 border-dashed rounded-3 bg-light p-4 text-center">
                <div class="dz-message">
                    <i data-feather="upload-cloud" class="icon-xl text-muted"></i>
                    <h5 class="mt-2">Arraste os arquivos ou clique aqui para enviar</h5>
                    <p class="text-muted small">Você pode adicionar múltiplos arquivos</p>
                </div>
            </div>
            <input type="hidden" name="attachments" id="attachments" value='{!! json_encode($topic->attachments) !!}'>

            <h4 class="text-center text-danger mt-5">**Não esqueça de salvar as alterações**</h4>
        </div>
    </div>
    <div class="card mb-5 shadow-sm">
        <div class="card-header">
            <h4 class="mb-0 d-flex align-items-center">
                <i data-feather="check-square" class="me-2"></i>
                Questionário
            </h4>
        </div>
        <div class="card-body">
            <div class="text-center">
                <button type="button" class="btn btn-outline-success btn-lg" data-bs-toggle="modal"
                    data-bs-target="#avaliacaoModal">
                    <i class="bi bi-gear-fill me-2 icon-sm"></i>
                    Configurar Questionário
                </button>
                <h4 class="text-center text-danger mt-5">**Não esqueça de salvar as alterações**</h4>
                <div class="modal fade" id="avaliacaoModal" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="exampleModalLabel">
                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    Editor de Questionário
                                </h1>
                            </div>
                            <div class="modal-body p-0 d-flex quiz-studio">
                                <div class="quiz-sidebar">
                                    <ul class="nav flex-column" id="perguntasNav">
                                    </ul>
                                    <div class="p-3">
                                        <button type="button" class="btn btn-outline-primary w-100"
                                            id="adicionarPergunta">
                                            <i class="bi bi-plus-circle me-2"></i>
                                            Nova Questão
                                        </button>
                                    </div>
                                </div>
                                <div class="quiz-content">
                                    <div id="perguntasContent">
                                    </div>
                                    <div id="emptyState" class="empty-state">
                                        <i class="bi bi-list-check"></i>
                                        <h3>Seu questionário está vazio</h3>
                                        <p>Clique em "Nova Questão" para começar.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="salvar-avaliacao">
                                    <i class="bi bi-check2-circle me-2"></i>
                                    Finalizar
                                </button>
                            </div>
                            <input type="hidden" id="avaliacaoJson" name="quiz"
                                value="{{ old('quiz', json_encode($topic->quizzes ?? [])) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center mt-10">
        <button type="submit" class="btn btn-primary btn-lg">
            <i data-feather="save" class="me-2 icon-xs"></i>
            Salvar
        </button>
    </div>
</section>
