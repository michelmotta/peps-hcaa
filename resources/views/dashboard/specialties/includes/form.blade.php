@csrf
<section>
    <div class="row">
        <div class="col-md-8">
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="title"
                    placeholder="Digite o título" name="name"
                    value="@if(isset($specialty)){{ $specialty->name }}@else{{ old('name') }} @endif"
                    required>
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="mb-3">
                <label for="file" class="form-label">Imagem Destaque</label>
                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                    name="file">
                <small class="d-block">Formatos permitidos: JPG|JPEG|PNG|GIF. Tamanho máximo: 2MB</small>
                @error('file')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <!-- Editor -->
        <div class="col-md-12">
            <div class="mt-2 mb-4">
                <label class="form-label">Descrição</label>
                <div id="editor">@if (isset($specialty)){!! $specialty->description !!}@else{!! old('description') !!}@endif
                </div>
                <input type="hidden" name="description" id="description"
                    value="@if(isset($specialty)){{ $specialty->description }}@else{{ old('description') }}@endif">
                @error('description')
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
