@csrf
<section>
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Especialidade Principal</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name', $specialty->name ?? '') }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb-3">
                <label for="file" class="form-label">Imagem Destaque</label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" name="file">
                <small class="d-block">Formatos permitidos: JPG|JPEG|PNG|GIF. Tamanho máximo: 2MB</small>
                @error('file')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label">Subespecialidades</label>
                <div id="subspecialties-wrapper">
                    @if (old('subspecialties'))
                        @foreach (old('subspecialties') as $sub)
                            <div class="input-group mb-2 subspecialty-item">
                                <input type="text" name="subspecialties[]" class="form-control"
                                    value="{{ $sub }}" placeholder="Subespecialidade" required>
                                <button class="btn btn-outline-danger remove-subspecialty" type="button">×</button>
                            </div>
                        @endforeach
                    @elseif (!empty($specialty->children))
                        @foreach ($specialty->children as $child)
                            <div class="input-group mb-2 subspecialty-item">
                                <input type="text" name="subspecialties[]" class="form-control"
                                    value="{{ $child->name }}" placeholder="Subespecialidade" required>
                                <button class="btn btn-outline-danger remove-subspecialty" type="button">×</button>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="add-subspecialty">
                    + Adicionar Subespecialidade
                </button>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i data-feather="save" class="me-2 icon-xs"></i> Salvar
        </button>
    </div>
</section>
