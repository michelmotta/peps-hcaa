@csrf
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="name" class="form-label">Nome do Setor</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                    placeholder="Digite o nome" name="name"
                    value="@if(isset($sector)){{ $sector->name }}@else{{ old('name') }}@endif"
                    required>
                @error('name')
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
