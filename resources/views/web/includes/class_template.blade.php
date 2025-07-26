<section class="classes-list py-5">
    <div class="container-fluid">
        <div class="container">
            <div class="row g-4">
                @forelse ($lessons as $lesson)
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
                                        <div class="card-author text-center mb-3">
                                            <a href="{{ route('web.teacher', $lesson->id) }}"
                                                class="d-inline-flex align-items-center gap-3 text-decoration-none text-reset">
                                                @if ($lesson->teacher && $lesson->teacher->file)
                                                    <img src="{{ asset('storage/' . $lesson->teacher->file->path) }}"
                                                        class="rounded-circle" alt="Avatar" width="40"
                                                        height="40" style="object-fit: cover;">
                                                @else
                                                    <img src="https://placehold.co/40x40/EBF4FF/7F9CF5?text={{ strtoupper(substr($lesson->teacher->name, 0, 1)) }}"
                                                        alt="Avatar" class="rounded-circle" width="40"
                                                        height="40">
                                                @endif
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
                                                <i class="bi bi-list-check me-1"></i>
                                                {{ $lesson->topics->count() }} tópicos
                                            </span>
                                            <span>
                                                <i class="bi bi-award me-1"></i>
                                                {{ $lesson->workload }} Horas
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
                @empty
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="bi bi-inbox"></i>
                        </div>
                        <h3 class="empty-state-title">Nenhum resultado encontrado.</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</section>
