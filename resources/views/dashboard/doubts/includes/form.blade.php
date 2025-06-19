@csrf
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="doubt" class="form-label">Dúvida</label>
                <input type="text" class="form-control @error('doubt') is-invalid @enderror" id="title"
                    placeholder="Digite a dúvida" name="doubt"
                    value="@if(isset($doubt)){{ $doubt->doubt }}@else{{ old('doubt') }}@endif"
                    required>
                @error('doubt')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <!-- Editor -->
        <div class="col-md-12">
            <div class="mt-2 mb-4">
                <label class="form-label">Resposta</label>
                <div id="editor">@if(isset($doubt)){!! $doubt->description !!}@else{!! old('description') !!}@endif</div>
                <input type="hidden" name="description" id="description"
                    value="@if(isset($doubt)){{ $doubt->description }}@else{{ old('description') }}@endif">
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
