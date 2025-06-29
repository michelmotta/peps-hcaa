@csrf
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="name" class="form-label">Título</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                    placeholder="Digite o título" name="title"
                    value="@if(isset($library)){{ $library->title }}@else{{ old('title') }} @endif"
                    required>
                @error('title')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-12">
            <div class="mb-3">
                <label for="file" class="form-label">Arquivo</label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                    name="file">
                <small class="d-block">Formato permitido: PDF. Tamanho máximo: 2MB</small>
                @error('file')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="text-center mt-5">
        <button type="submit" class="btn btn-primary btn-lg">
            <i data-feather="save" class="me-2 icon-xs"></i>
            Salvar
        </button>
    </div>
</section>
