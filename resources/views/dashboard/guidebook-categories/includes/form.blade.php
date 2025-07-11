@csrf
<div class="row">
    {{-- Category Name --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label for="name" class="form-label">Nome da Categoria</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                placeholder="Ex: Primeiros Passos" name="name" value="{{ old('name', $category?->name) }}" required>
            @error('name')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>

    {{-- Icon Name --}}
    <div class="col-md-6">
        <div class="mb-3">
            <label for="icon" class="form-label">Ícone</label>
            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon"
                placeholder="Ex: book-open" name="icon" value="{{ old('icon', $category?->icon ?? 'book-open') }}">
            <small class="text-muted">
                Use um nome de ícone da biblioteca <a href="https://feathericons.com/" target="_blank">Feather
                    Icons</a>.
            </small>
            @error('icon')
                <span class="invalid-feedback" role="alert">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
<div class="text-center mt-4">
    <button type="submit" class="btn btn-primary btn-lg px-5">
        <i data-feather="save" class="me-2 icon-xs"></i>
        Salvar
    </button>
</div>
