@csrf
<section>
    <div class="row g-4 d-flex align-items-start">
        {{-- COLUNA ESQUERDA --}}
        <div class="col-lg-7 h-100">
            <fieldset class="h-100">
                <legend class="h5 mb-4">Detalhes da Especialidade</legend>

                <div class="mb-4">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name', $specialty->name ?? '') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="subspecialties" class="form-label">Subespecialidades</label>
                    <input type="text" id="subspecialties" name="subspecialties" class="form-control"
                        value="{{ old('subspecialties', isset($specialty) ? $specialty->children->pluck('name')->implode(',') : '') }}">
                    <small class="form-text text-muted mt-2">
                        Digite e pressione enter ou vírgula para adicionar uma nova subespecialidade.
                    </small>
                </div>
            </fieldset>
        </div>

        {{-- COLUNA DIREITA --}}
        <div class="col-lg-5 h-100">
            <fieldset class="h-100 w-100 d-flex flex-column justify-content-center align-items-center text-center">
                <legend class="h5 mb-4">Imagem de Destaque</legend>

                <img id="image-preview"
                    src="{{ isset($specialty->file) ? asset('storage/' . $specialty->file->path) : 'https://placehold.co/300x150.png?text=Selecione+uma+Imagem' }}"
                    alt="Prévia da imagem" class="img-fluid rounded shadow-sm mb-3 @error('file') is-invalid @enderror"
                    style="max-height: 250px;">

                <input type="file" name="file" id="file" class="d-none @error('file') is-invalid @enderror">

                <label for="file" class="btn btn-outline-secondary mb-2">
                    <i data-feather="upload" class="me-1"></i>
                    {{ isset($specialty->file) ? 'Alterar Imagem' : 'Selecionar Imagem' }}
                </label>

                <small class="text-muted">JPG, PNG, GIF. Máximo 2MB.</small>

                @error('file')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </fieldset>
        </div>
    </div>

    {{-- BOTÃO DE SUBMISSÃO --}}
    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg px-5">
            Salvar Alterações
        </button>
    </div>
</section>
