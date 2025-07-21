<section class="questions-section">
    <h2 class="section-title text-center">
        <i class="bi bi-question-circle me-2"></i>
        Dúvidas
    </h2>
    <div class="qa-list">
        @foreach ($lesson->doubts as $doubt)
            <div class="qa-item">
                <p class="qa-meta">
                    <i class="bi bi-person-fill"></i> {{ $doubt->user->name }} &nbsp;
                    <i class="bi bi-calendar-event"></i>
                    {{ $doubt->created_at_formatted }}
                </p>
                <p class="student-question">
                    <i class="bi bi-chat-left-text"></i> {{ $doubt->doubt }}
                </p>
                @if (!empty(trim(strip_tags($doubt->description))))
                    <div class="teacher-answer">
                        <div class="mb-2">
                            <small>
                                <i class="bi bi-check2-circle"></i> Respondido por
                                <strong>{{ $lesson->teacher->name }}</strong> em
                                <strong>{{ $doubt->answered_at_formatted }}</strong>.
                            </small>
                        </div>
                        {!! $doubt->description !!}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    <div class="text-center">
        <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalPergunta">
            <i class="bi bi-chat-dots me-2"></i>
            Faça sua pergunta
        </button>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalPergunta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="exampleModalLabel">
                        A pergunta será respondida pelo(a) professor(a)</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="doubtForm" class="question-form" method="POST"
                        action="{{ route('web.doubt-create', $lesson) }}">
                        @csrf
                        <label for="question">Pergunta</label>
                        <textarea id="question" class="form-control @error('doubt') is-invalid @enderror" name="doubt"
                            placeholder="Digite sua dúvida aqui..." required></textarea>
                        {{-- Validation Error --}}
                        @error('doubt')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <div class="text-center">
                            <button type="submit">
                                <i class="bi bi-send"></i> Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
