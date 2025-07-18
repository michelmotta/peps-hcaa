<section class="curso-profile py-4 bg-light">
    <div class="container">
        <div class="curso-profile-card">
            <!-- Course Info and Teacher Info (Side by side) -->
            <div class="row justify-content-center mt-4">
                <div class="col-md-8 mb-5">
                    <div class="curso-profile-card-infos">
                        <div>
                            <img src="{{ asset('storage/' . $lesson->file->path) }}" class="img-fluid curso-thumbnail"
                                alt="Curso de Cardiologia">
                        </div>
                        <div class="p-4">
                            <div class="row align-items-center">
                                <div class="col-md-9">
                                    <h3 class="titulo-curso mb-0">{{ $lesson->name }}</h3>
                                </div>
                                <div class="col-md-3 text-end">
                                    <form action="{{ route('web.subscribe', $lesson) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-primary px-4 py-2 fw-semibold">
                                            <i class="bi bi-pencil-square me-2"></i> Inscrever-se
                                        </button>
                                    </form>
                                </div>
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
                                                    {{ $index + 1 }}. {{ $topic->title }}
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
                <!-- Right Column (Teacher Info) -->
                <div class="col-md-4">
                    <div class="teacher-info">
                        <div class="teacher-thumbnail">
                            <img src="{{ asset('storage/' . $lesson->teacher->file->path) }}" class="img-fluid"
                                alt="Professor Jesse Pinkman">
                        </div>
                        <div class="p-4"> <!-- Add padding to the text part only -->
                            <h6 class="fw-bold text-center mb-0">{{ $lesson->teacher->name }}</h6>
                            <p class="text-muted text-center mb-3">{{ $lesson->teacher->expertise }}</p>
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
