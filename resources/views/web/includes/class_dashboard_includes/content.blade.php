<section class="attachments-section">
    <div class="faqs mt-3">
        <h4 class="text-center mb-4">
            <i class="bi bi-list-check me-1"></i>
            Tópicos da Aula
        </h4>
        <div class="accordion" id="faqAccordion">
            @foreach ($lesson->topics as $index => $topic)
                <div class="accordion-item mb-3 border-0 rounded-3 overflow-hidden">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed shadow-none" type="button" data-bs-toggle="collapse"
                            data-bs-target="#faqtopic-{{ $topic->id }}">
                            <i class="fas fa-graduation-cap me-2"></i>
                            {{ $index + 1 }}.
                            {{ $topic->title }}
                        </button>
                    </h2>
                    <div id="faqtopic-{{ $topic->id }}" class="accordion-collapse collapse"
                        data-bs-parent="#faqAccordion">
                        <div class="accordion-body bg-light">
                            {!! $topic->description !!}

                            <div class="attachment-list">
                                <h4 class="text-center mt-5 mb-4">
                                    <i class="bi bi-paperclip me-1"></i>
                                    Materiais Complementares
                                </h4>
                                <ul class="list-unstyled mt-3">
                                    @foreach ($topic->attachments as $attachment)
                                        <li class="attachment-item d-flex align-items-center p-3 mb-2 rounded">
                                            <i class="bi bi-file-earmark-text text-danger me-3 fs-4"></i>
                                            <div class="flex-grow-1">
                                                <strong>{{ $attachment['name'] }}</strong>
                                                <div class="text-muted small">Enviado em
                                                    {{ $attachment['date'] }}</div>
                                            </div>
                                            <a href="{{ Storage::url($attachment['path']) }}"
                                                class="btn btn-sm btn-outline-primary d-flex align-items-center"
                                                download>
                                                <i class="bi bi-download me-1"></i> Baixar
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
