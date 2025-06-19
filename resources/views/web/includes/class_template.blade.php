<section class="classes-list py-5">
    <div class="container-fluid">
        <div class="container">
            <div class="row g-4">
                @foreach ($lessons as $lesson)
                    <div class="col-md-6">
                        <div class="row class-card g-0 align-items-stretch bg-white overflow-hidden mb-4">
                            <div class="col-md-4">
                                <div class="thumbnail position-relative h-100 overflow-hidden">
                                    <img src="{{ asset('storage/' . $lesson->file->path) }}" alt="Imagem da aula"
                                        class="img-fluid w-100 h-100 object-fit-cover">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="class-infos h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <h4>{{ $lesson->name }}</h4>
                                        <div style="text-align: justify">
                                            {!! Str::limit(strip_tags($lesson->description), 120) !!}
                                        </div>
                                        <div
                                            class="card-author d-flex align-items-center justify-content-center gap-3 mt-4 flex-wrap text-center">
                                            <a href="{{ route('web.teacher', $lesson->id) }}"
                                                class="d-flex align-items-center gap-2 text-decoration-none text-reset">
                                                <img src="{{ asset('storage/' . $lesson->teacher->file->path) }}"
                                                    alt="{{ $lesson->teacher->name }}" width="40"
                                                    class="rounded-circle">
                                                <span>{{ $lesson->teacher->name }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div
                                        class="class-meta d-flex justify-content-center gap-4 small text-muted text-center">
                                        <span>
                                            <i class="bi bi-bookmark"></i>
                                            {{ $lesson->specialty->name }}
                                        </span>
                                        <span>
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $lesson->workload }} Horas
                                        </span>
                                        <span>
                                            <i class="bi bi-list-check me-1"></i>
                                            {{ $lesson->topics->count() }} tópicos
                                        </span>
                                    </div>
                                    <hr>
                                    <div class="text-center">
                                        <a href="{{ route('web.class', $lesson->id) }}"
                                            class="btn fw-bold read-more-btn">
                                            <i class="bi bi-journal-text me-1"></i> Ver Aula
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
