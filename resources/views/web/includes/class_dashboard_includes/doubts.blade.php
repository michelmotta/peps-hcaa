<section class="questions-section">
    <div class="container">
        <h2 class="section-title"><span>Dúvidas e Respostas</span></h2>
        @auth
            <div class="accordion question-form-accordion mb-5" id="askQuestionAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseAskQuestion">
                            <i class="bi bi-chat-dots me-2"></i> Clique aqui para enviar sua dúvida
                        </button>
                    </h2>
                    <div id="collapseAskQuestion" class="accordion-collapse collapse"
                        data-bs-parent="#askQuestionAccordion">
                        <div class="accordion-body">
                            <form id="doubtForm" method="POST" action="{{ route('web.doubt-create', $lesson) }}">
                                @csrf
                                <div class="mb-3">
                                    <textarea class="form-control" name="doubt" rows="4" placeholder="Digite sua dúvida aqui..." required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> Enviar Pergunta
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
        <div class="chat-feed">
            @forelse ($lesson->doubts as $doubt)
                <div class="chat-message student-message">
                    <div class="chat-avatar">
                        <img src="{{ asset('storage/' . $doubt->user->file->path) }}" alt="{{ $doubt->user->name }}">
                    </div>
                    <div class="message-content">
                        <div class="chat-bubble">
                            <p>{{ $doubt->doubt }}</p>
                        </div>
                        <div class="chat-meta">
                            <strong>{{ $doubt->user->name }}</strong> &middot; {{ $doubt->created_at_formatted }}
                        </div>
                    </div>
                </div>
                @if (!empty(trim(strip_tags($doubt->description))))
                    <div class="chat-message teacher-message">
                        <div class="chat-avatar">
                            <img src="{{ asset('storage/' . $lesson->teacher->file->path) }}"
                                alt="{{ $lesson->teacher->name }}">
                        </div>
                        <div class="message-content">
                            <div class="chat-bubble">
                                {!! $doubt->description !!}
                            </div>
                            <div class="chat-meta">
                                <strong>{{ $lesson->teacher->name }}</strong> &middot;
                                {{ $doubt->answered_at_formatted }}
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center py-5" id="empty-chat-message">
                    <i class="bi bi-chat-quote display-4 text-muted"></i>
                    <h4 class="mt-3">Nenhuma dúvida por aqui ainda</h4>
                    <p class="text-muted">Seja o primeiro a fazer uma pergunta!</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
