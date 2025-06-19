@csrf
<section>
    <div class="row">
        <div class="col-md-9">
            <div class="mb-3">
                <label for="user_id" class="form-label">Estudante</label>
                <select id="user-select" class="form-control" name="user_id">
                    @if (isset($student))
                        <option value="{{ $student->user->id }}" selected>{{ $student->user->name }}</option>
                    @endif
                </select>
                <small>Digite e selecione um estudante do sistema</small>
                @error('user_id')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label class="form-label" for="finished">Status</label>
                <select class="form-select @error('finished') is-invalid @enderror" aria-label="Default select example"
                    name="finished" required>
                    <option value="false" @if(isset($student) && $student->finished === false) selected @endif>Em Andamento</option>
                    <option value="true" @if(isset($student) && $student->finished === true) selected @endif>Concluído</option>
                </select>
                @error('finished')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="created_at" class="form-label">Data de Início</label>
                <input type="text" class="date form-control @error('created_at') is-invalid @enderror"
                    id="created_at" placeholder="Digite a data de início" name="created_at"
                    value="@if(isset($student)){{ $student->created_at_formatted }}@else{{ date('d/m/Y') }}@endif"
                    required>
                @error('created_at')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="finished_at" class="form-label">Data de Término</label>
                <input type="text" class="date form-control @error('finished_at') is-invalid @enderror"
                    id="finished_at" placeholder="Digite a data de término" name="finished_at"
                    value="@if(isset($student)){{ $student->finished_at_formatted }}@else{{ old('finished_at') }}@endif">
                @error('finished_at')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="score" class="form-label">Nota Final</label>
                <input type="number" class="form-control @error('score') is-invalid @enderror" id="score"
                    placeholder="Digite a nota" name="score"
                    value="@if(isset($student)){{ $student->score }}@else{{ old('score') }}@endif"
                    min="0" max="10">
                @error('score')
                    <span class="invalid-feedback" role="alert">
                        {{ $message }}
                    </span>
                @enderror
            </div>
        </div>
    </div>
    <div class="text-center mt-5">
        <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
        <button type="submit" class="btn btn-primary btn-lg">
            <i data-feather="save" class="me-2 icon-xs"></i>
            Salvar
        </button>
    </div>
</section>
