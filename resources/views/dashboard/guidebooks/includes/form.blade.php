@csrf
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="title" class="form-label">Título</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                    placeholder="Digite o título" name="title"
                    value="@if(isset($guidebook)){{ $guidebook->title }}@else{{ old('title') }}@endif"
                    required>
                @error('title')
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
                <div id="editor">@if (isset($guidebook)){!! $guidebook->description !!}@else{!! old('description') !!}@endif
                </div>
                <input type="hidden" name="description" id="description"
                    value="@if(isset($guidebook)){{ $guidebook->description }}@else{{ old('description') }}@endif">
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
