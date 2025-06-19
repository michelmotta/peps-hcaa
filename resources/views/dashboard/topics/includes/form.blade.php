@csrf
<section>
    <div class="card mb-5 ">
        <div class="card-header">
            <h4 class="mb-0">Apresentação</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            placeholder="Digite o título" name="title"
                            value="@if(isset($topic)){{ $topic->title }}@else{{ old('title') }}@endif" required>
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
                        <textarea name="resume" id="resume" class="form-control">@if(isset($topic)){{ $topic->resume }}@else{{ old('resume') }}@endif</textarea>
                        @error('resume')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            </fieldset>
        </div>
    </div>
    <div class="card mb-5 ">
        <div class="card-header">
            <h4 class="mb-0">Conteúdo</h4>
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
                <!-- Editor -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Conteúdo</label>
                        <div id="editor">@if(isset($topic)){!! $topic->description !!}@else{!! old('description') !!}@endif</div>
                        <input type="hidden" name="description" id="description"
                            value="@if(isset($topic)){{ $topic->description }}@else{{ old('description') }}@endif">
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
    <div class="card mb-5 ">
        <div class="card-header">
            <h4 class="mb-0">Anexos</h4>
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
                                                @if(isset($file['extension']) && in_array($file['extension'], ['.mp4', '.mov', '.avi', '.mkv']))
                                                    <i data-feather="play-circle" class="icon-xl"></i>
                                                @elseif(isset($file['extension']) && in_array($file['extension'], ['.png', '.jpg', '.jpeg', '.gif', '.bmp', '.webp']))   
                                                    <i data-feather="image" class="icon-xl"></i>
                                                @else
                                                    <i data-feather="file-text" class="icon-xl"></i>
                                                @endif
                                            </a>
                                            <div class="ms-3">
                                                <h6 class="mb-0">{{ $file['name'] }}</h6>
                                                <small>@if(isset($file['extension'])){{ round($file['size'] / (1024 * 1024), 2) }} MB @endif</small>
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
            {{-- Dropzone Upload Area --}}
            <div id="dropzone" class="dropzone border border-2 border-primary rounded bg-light p-3 mb-3"></div>

            <!-- Hidden input for attachments -->
            <input type="hidden" name="attachments" id="attachments" value='@json($savedAttachments)'>

            <h4 class="text-center text-danger mt-5">**Não esqueça de salvar as alterações**</h4>
        </div>
    </div>
    <div class="card mb-5 ">
        <div class="card-header">
            <h4 class="mb-0">Questionário</h4>
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
                                    Configurar
                                </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h2>Configurar Questionário</h2>
                                <div id="perguntasContainer" class="accordion mb-3"></div>

                                <button type="button" class="btn btn-outline-success mb-3 mt-8"
                                    id="adicionarPergunta">
                                    <i class="bi bi-plus-circle me-2 icon-xs"></i>
                                    Adicionar Questão
                                </button>

                                <input type="hidden" id="avaliacaoJson" name="quiz" value="{{ old('quiz', json_encode($topic->quizzes ?? [])) }}">
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
