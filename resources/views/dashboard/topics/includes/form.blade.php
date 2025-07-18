@csrf
<section>
    <div class="card mb-5 shadow-sm">
        <div class="card-header">
            {{-- Icon added here --}}
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
                        <textarea name="resume" id="resume" class="form-control">@if (isset($topic)){{ $topic->resume }}@else{{ old('resume') }}@endif</textarea>
                        @error('resume')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            {{-- The user code had a closing fieldset tag here, which might be a typo as there's no opening tag. I'm omitting it for valid HTML. --}}
            {{-- </fieldset> --}}
        </div>
    </div>
    <div class="card mb-5 shadow-sm">
        <div class="card-header">
             {{-- Icon added here --}}
            <h4 class="mb-0 d-flex align-items-center">
                <i data-feather="book-open" class="me-2"></i>
                Conteúdo
            </h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="file" class="form-label">Vídeo</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                            name="file">
                        <small class="d-block">Formatos permitidos: MP4|WEBM. Tamanho máximo: 100MB</small>
                        @error('file')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Conteúdo</label>
                        <div id="editor">
                            @if (isset($topic)){!! $topic->description !!}@else{!! old('description') !!}@endif
                        </div>
                        <input type="hidden" name="description" id="description"
                            value="@if (isset($topic)) {{ $topic->description }}@else{{ old('description') }} @endif">
                        @error('description')
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
             {{-- Icon added here --}}
            <h4 class="mb-0 d-flex align-items-center">
                <i data-feather="paperclip" class="me-2"></i>
                Anexos
            </h4>
        </div>
        <div class="card-body">
            @php
                $savedAttachments = $topic->attachments ?? '[]';
            @endphp
            @if (is_array($savedAttachments))
                <div id="existing-attachments" class="row mb-3">
                    @foreach ($savedAttachments as $file)
                        <div class="col-md-3 mb-3 attachment-card" data-path="{{ $file['path'] }}">
                            <div class="card card-bordered  mb-4 ">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <a href="{{ Storage::url($file['path']) }}" data-fancybox>
                                                @if (isset($file['extension']) && in_array($file['extension'], ['.mp4', '.mov', '.avi', '.mkv']))
                                                    <i data-feather="play-circle" class="icon-xl"></i>
                                                @elseif(isset($file['extension']) && in_array($file['extension'], ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.webp']))
                                                    <i data-feather="image" class="icon-xl"></i>
                                                @else
                                                    <i data-feather="file-text" class="icon-xl"></i>
                                                @endif
                                            </a>
                                            <div class="ms-3">
                                                <h6 class="mb-0">{{ $file['name'] }}</h6>
                                                <small>
                                                    @if (isset($file['extension']))
                                                        {{ round($file['size'] / (1024 * 1024), 2) }} MB
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger delete-btn">
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
            {{-- Dropzone Upload Area --}}
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
             {{-- Icon added here --}}
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
                            <div class="modal-body">
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