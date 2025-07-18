<section class="classes-list py-5">
    <div class="container-fluid">
        <div class="container">
            <div class="row g-4">
                @foreach ($lessons as $lesson)
                    <div class="col-md-6">
                        <div
                            class="row class-card g-0 align-items-stretch bg-white overflow-hidden mb-4 position-relative">
                            <div class="col-md-4">
                                <div class="thumbnail position-relative h-100 overflow-hidden">
                                    @auth
                                        @if ($subscription = $lesson->subscriptions->first())
                                            <div
                                                class="status-banner {{ $subscription->pivot->finished ? 'finished' : 'in-progress' }}">
                                                @if ($subscription->pivot->finished)
                                                    <i class="bi bi-check-circle-fill me-1"></i> Concluído
                                                @else
                                                    <i class="bi bi-hourglass-split me-1"></i> Em andamento
                                                @endif
                                            </div>
                                        @endif
                                    @endauth

                                    <img src="{{ asset('storage/' . $lesson->file->path) }}" alt="Imagem da aula"
                                        class="img-fluid w-100 h-100 object-fit-cover">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="class-infos h-100 d-flex flex-column justify-content-between p-4">

                                    {{-- Top content block --}}
                                    <div>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            @foreach ($lesson->specialties as $specialty)
                                                <a href="#"
                                                    class="badge bg-light text-dark text-decoration-none fw-normal">
                                                    <i class="bi bi-tag me-1"></i>
                                                    {{ $specialty->name }}
                                                </a>
                                            @endforeach
                                        </div>

                                        <h4>{{ $lesson->name }}</h4>

                                        <div class="mb-3">
                                            {!! Str::limit(strip_tags($lesson->description), 120) !!}
                                        </div>

                                        {{-- MODIFIED: Added professor's expertise --}}
                                        <div class="card-author text-center mb-3">
                                            <a href="{{ route('web.teacher', $lesson->id) }}"
                                                class="d-inline-flex align-items-center gap-3 text-decoration-none text-reset">
                                                <img src="{{ asset('storage/' . $lesson->teacher->file->path) }}"
                                                    alt="{{ $lesson->teacher->name }}" width="40" height="40"
                                                    class="rounded-circle">
                                                <div class="text-start">
                                                    <p class="mb-0 fw-bold">{{ $lesson->teacher->name }}</p>
                                                    <small class="text-muted">{{ $lesson->teacher->expertise }}</small>
                                                </div>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                        <div class="class-meta d-flex gap-4 small text-muted">
                                            <span>
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $lesson->workload }} Horas
                                            </span>
                                            <span>
                                                <i class="bi bi-list-check me-1"></i>
                                                {{ $lesson->topics->count() }} tópicos
                                            </span>
                                        </div>

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
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
