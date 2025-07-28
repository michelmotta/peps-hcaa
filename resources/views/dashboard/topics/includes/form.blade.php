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
                            value="@if (isset($topic)) {{ $topic->title }}@else{{ old('title') }} @endif"
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
                        <textarea name="resume" id="resume" class="form-control">
@if (isset($topic))
{{ $topic->resume }}@else{{ old('resume') }}
@endif
</textarea>
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
                        <label for="file" class="form-label">Vídeo</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                            name="file" accept="video/mp4" data-upload-url="{{ route('dashboard.videos.store') }}">
                        <small class="d-block">Formatos permitidos: MP4|WEBM. Tamanho máximo: 100MB</small>
                        @error('file')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <input type="hidden" name="video_id" id="video-id-input" value="{{ $topic->video->id ?? '' }}">
                <div class="col-md-12 mb-3" id="video-preview-container"
                    @if (!isset($topic) || !$topic->video) style="display: none;" @endif>
                    <div class="card border-success bg-success-subtle shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="position-relative" id="video-thumbnail-wrapper">
                                        <a href="{{ isset($topic) && $topic->video ? Storage::url($topic->video->path) : '#' }}"
                                            @if (isset($topic) && $topic->video) data-fancybox @endif>
                                            <img id="video-thumbnail-preview"
                                                src="{{ isset($topic) && $topic->video ? Storage::url($topic->video->thumbnail_path) : '' }}"
                                                alt="Thumbnail do vídeo" class="img-fluid rounded"
                                                style="width: 160px; height: 90px; object-fit: cover;">
                                            <div class="position-absolute top-50 start-50 translate-middle"
                                                style="pointer-events: none;">
                                                <i class="bi bi-play-circle-fill text-white"
                                                    style="font-size: 2.5rem; opacity: 0.7; text-shadow: 0 0 8px rgba(0,0,0,0.5);"></i>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="col">
                                    <h6 class="mb-1 d-flex align-items-center text-success-emphasis">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        Vídeo Carregado
                                    </h6>
                                    <p class="mb-1 fw-bold text-dark" id="video-file-name">
                                        {{ $topic->video->name ?? 'nome_do_arquivo.mp4' }}</p>
                                    <p class="text-muted small mb-3" id="video-file-size">
                                        {{ isset($topic) && $topic->video ? round($topic->video->size / (1024 * 1024), 2) . ' MB' : '' }}
                                    </p>
                                    <button type="button" id="remove-video-btn" class="btn btn-sm btn-danger"
                                        data-base-url="{{ url('dashboard/videos') }}"
                                        data-delete-url="{{ isset($topic) && $topic->video ? route('dashboard.videos.destroy', $topic->video->id) : '' }}">
                                        <i class="bi bi-trash me-1"></i> Trocar Vídeo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3" id="progress-container" style="display: none;">
                    <div class="progress" style="height: 25px;">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated fs-6"
                            role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                            aria-valuemax="100"></div>
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
        <div class="card-header">
            <h4 class="mb-0 d-flex align-items-center">
                <i data-feather="paperclip" class="me-2"></i>
                Anexos
            </h4>
        </div>
        <div class="card-body">
            @php
                $savedAttachments = $topic->attachments ?? collect();
            @endphp

            @if ($savedAttachments->isNotEmpty())
                <div id="existing-attachments" class="row mb-3">
                    @foreach ($savedAttachments as $file)
                        <div class="col-md-3 mb-3 attachment-card" data-path="{{ $file['path'] ?? '' }}">
                            <div class="card card-bordered mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <a href="{{ Storage::url($file['path'] ?? '') }}" data-fancybox>
                                                @php
                                                    $ext = strtolower($file['extension'] ?? '');
                                                @endphp

                                                @if (in_array($ext, ['mp4', 'mov', 'avi', 'mkv']))
                                                    <i data-feather="play-circle" class="icon-xl"></i>
                                                @elseif (in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp']))
                                                    <i data-feather="image" class="icon-xl"></i>
                                                @else
                                                    <i data-feather="file-text" class="icon-xl"></i>
                                                @endif
                                            </a>
                                            <div class="ms-3">
                                                <h6 class="mb-0">{{ $file['name'] ?? 'Arquivo' }}</h6>
                                                <small>
                                                    {{ isset($file['size']) ? round($file['size'] / (1024 * 1024), 2) . ' MB' : '' }}
                                                </small>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-btn">
                                                <i data-feather="trash-2" class="icon-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
            <div id="dropzone" class="dropzone border-2 border-dashed rounded bg-light p-4 text-center">
                <div class="dz-message">
                    <i data-feather="upload-cloud" class="icon-xl text-muted"></i>
                    <h5 class="mt-2">Arraste os arquivos ou clique aqui para enviar</h5>
                    <p class="text-muted small">Você pode adicionar múltiplos arquivos</p>
                </div>
            </div>
            <input type="hidden" name="attachments" id="attachments" value='@json($savedAttachments)'>
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
                                    <i class="bi bi-gear-fill me-2 icon-xs"></i>
                                    Configurar Questionário
                                </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-light">
                                <div id="perguntasContainer" class="accordion mb-3"></div>

                                <button type="button" class="btn btn-outline-success mb-3 mt-8"
                                    id="adicionarPergunta">
                                    <i class="bi bi-plus-circle me-2 icon-xs"></i>
                                    Adicionar Questão
                                </button>

                                <input type="hidden" id="avaliacaoJson" name="quiz"
                                    value="{{ old('quiz', json_encode($topic->quizzes ?? [])) }}">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="salvar-avaliacao">
                                    <i class="bi bi-check2-circle me-2 icon-sm"></i>
                                    Finalizar
                                </button>
                            </div>
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
