<section class="filter-form-section py-4">
    <div class="container">
        <form type="GET" action="{{ route('web.classes') }}" class="p-4 bg-white border rounded-3">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-12">
                    <label for="q" class="form-label">O que você quer aprender?</label>
                    <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}"
                        placeholder="Ex: Anatomia, Cuidados Paliativos...">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="specialty_id" class="form-label">Especialidade</label>
                    <select class="form-select" id="specialty_id" name="specialty_id">
                        <option value="">Todas</option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty->id }}" @selected(request('specialty_id') == $specialty->id)>
                                {{ $specialty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="teacher_id" class="form-label">Professor(a)</label>
                    <select class="form-select" id="teacher_id" name="teacher_id">
                        <option value="">Todos</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}" @selected(request('teacher_id') == $teacher->id)>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="sort_by" class="form-label">Ordenar por</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="newest" @selected(request('sort_by', 'newest') == 'newest')>Mais Recentes</option>
                        <option value="oldest" @selected(request('sort_by') == 'oldest')>Mais Antigas</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6 d-grid gap-2 d-md-flex">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-filter me-1"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
