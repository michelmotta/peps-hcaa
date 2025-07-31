@csrf
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="subject" class="form-label">Assunto</label>
                <input type="text" class="form-control @error('subject') is-invalid @enderror" id="title"
                    placeholder="Digite o assunto" name="subject"
                    value="@if(isset($message)){{ $message->subject }}@else{{ old('subject') }}@endif"
                    required>
                @error('subject')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-12">
            <div class="mt-2 mb-4">
                <label class="form-label">Comunicado</label>
                <div id="editor">@if(isset($message)){!! $message->description !!}@else{!! old('description') !!}@endif</div>
                <input type="hidden" name="description" id="description"
                    value="@if(isset($message)){{ $message->description }}@else{{ old('description') }}@endif">
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
