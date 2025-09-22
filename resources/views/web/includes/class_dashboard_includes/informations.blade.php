<section class="curso-profile">
    <div class="container">
        <div class="curso-profile-card">
            <div class="row justify-content-center mt-4">
                <div class="col-md-12 mb-5">
                    <div class="curso-profile-card-infos shadow-none">
                        <div>
                            <img src="{{ asset('storage/' . $lesson->file->path) }}" class="img-fluid curso-thumbnail"
                                alt="Curso de Cardiologia">
                        </div>
                        <div class="p-4">
                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <h3 class="titulo-curso mb-1">{{ $lesson->name }}</h3>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @foreach ($lesson->specialties as $specialty)
                                    <a href="#" class="badge bg-light text-dark text-decoration-none fw-normal">
                                        <i class="bi bi-tag me-1"></i>
                                        {{ $specialty->name }}
                                    </a>
                                @endforeach
                            </div>
                            <div class="mt-3 text-muted">
                                {!! $lesson->description !!}
                            </div>
                            <div class="fs-6 d-flex justify-content-start gap-4 small text-muted text-center mt-3">
                                <span>
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $lesson->workload }} Horas
                                </span>
                                <span>
                                    <i class="bi bi-list-check me-1"></i>
                                    {{ $lesson->topics->count() }} tópicos
                                </span>
                                <span>
                                    <i class="bi bi-mortarboard me-1"></i>
                                    {{ $lesson->subscriptions->count() }} Estudantes
                                </span>
                            </div>
                            <div class="faqs mt-5">
                                <h4 class="text-center mb-4">Tópicos da Aula</h4>
                                <div class="accordion" id="faqAccordion">
                                    @foreach ($lesson->topics as $index => $topic)
                                        <div class="accordion-item mb-3 border-0 rounded-3 overflow-hidden">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed shadow-none" type="button"
                                                    data-bs-toggle="collapse" data-bs-target="#faq-{{ $topic->id }}">
                                                    <i class="fas fa-graduation-cap me-2"></i>
                                                    {{ $topic->title }}
                                                </button>
                                            </h2>
                                            <div id="faq-{{ $topic->id }}" class="accordion-collapse collapse"
                                                data-bs-parent="#faqAccordion">
                                                <div class="accordion-body bg-light">
                                                    {!! $topic->resume !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="teacher-info shadow-none">
                        <div class="teacher-thumbnail">
                            <img src="{{ $lesson->teacher?->file?->path
                                ? asset('storage/' . $lesson->teacher->file->path)
                                : 'https://placehold.co/150x150/EBF4FF/7F9CF5?text=' . urlencode($lesson->teacher->name ?? 'P') }}"
                                class="img-fluid rounded-circle" alt="{{ $lesson->teacher->name ?? 'Professor' }}">
                        </div>
                        <div class="p-4">
                            <h6 class="fw-bold text-center mb-0">
                                {{ $lesson->teacher->name }}</h6>
                            <p class="text-muted text-center mb-3">
                                {{ $lesson->teacher->expertise }}</p>
                            <div class="text-muted small text-justify teacher-biography">
                                {{ $lesson->teacher->biography }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
