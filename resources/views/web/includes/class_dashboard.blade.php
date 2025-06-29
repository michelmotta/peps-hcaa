<section class="curso-tabs py-5 bg-light">
    <div class="container">
        <div class="row g-4 flex-lg-nowrap">
            <!-- Coluna Lateral: Tópicos -->
            <div class="col-12 col-lg-4">
                <div class="course-topics p-4 bg-white rounded-4 d-flex flex-column">
                    <h5 class="mb-4 fw-bold text-center">Tópicos da Aula</h5>

                    @foreach ($lesson->topics as $topic)
                        @php
                            $isWatched = in_array($topic->id, $watchedTopicIds ?? []);
                        @endphp
                        <!-- Tópico assistido -->
                        <div class="topic-item {{ $isWatched ? 'watched' : '' }}"
                            data-video="{{ asset('storage/' . $topic->video->path) }}"
                            data-topic-id="{{ $topic->id }}">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $topic->video->thumbnail_path) }}" alt="Thumb"
                                    class="topic-thumb">
                            </div>
                            <div class="topic-info mt-1">
                                <h6 class="mb-1">{{ $topic->title }}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small
                                        class="text-muted">{{ humanAbbreviatedTime($topic->video->duration) }}</small>
                                    @if ($isWatched)
                                        <span class="badge badge-watched">Assistido</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @php
                        $totalTopics = count($lesson->topics);
                        $watchedCount = count($watchedTopicIds ?? []);
                        $progressPercent = $totalTopics > 0 ? round(($watchedCount / $totalTopics) * 100) : 0;
                    @endphp
                    <!-- Progress bar fixed at bottom -->
                    <div class="course-progress-bar mt-auto pt-3">
                        <small class="d-block text-muted text-end mb-1">
                            {{ $progressPercent }}% concluído
                        </small>
                        <div class="progress" style="height: 6px; background-color: #e9ecef;">
                            <div class="progress-bar" role="progressbar"
                                style="width: {{ $progressPercent }}%; background-color: #133b6a;"
                                aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Coluna Principal: Vídeo + Abas -->
            <div class="col-12 col-lg-8 d-flex flex-column">
                <div class="bg-player">
                    <!-- Vídeo -->
                    <div class="video-container mb-4">
                        <div class="overflow-hidden shadow">
                            <video controls class="js-player player w-100"></video>
                        </div>
                    </div>

                    <!-- Abas -->
                    <ul class="nav nav-tabs justify-content-center mb-4" id="cursoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="conteudo-tab" data-bs-toggle="tab"
                                data-bs-target="#conteudo" type="button" role="tab">
                                <i class="bi bi-journal-text me-1"></i> Informações
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="anexos-tab" data-bs-toggle="tab" data-bs-target="#anexos"
                                type="button" role="tab">
                                <i class="bi bi-paperclip me-1"></i> Conteúdo
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="duvidas-tab" data-bs-toggle="tab" data-bs-target="#duvidas"
                                type="button" role="tab">
                                <i class="bi bi-question-circle me-1"></i> Dúvidas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="avaliacao-tab" data-bs-toggle="tab" data-bs-target="#avaliacao"
                                type="button" role="tab">
                                <i class="bi bi-bar-chart-line me-1"></i> Quiz
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="certificado-tab" data-bs-toggle="tab"
                                data-bs-target="#certificado" type="button" role="tab">
                                <i class="bi bi-patch-check me-1"></i> Certificado
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback"
                                type="button" role="tab">
                                <i class="bi bi-chat-dots me-1"></i> Avaliar
                            </button>
                        </li>
                    </ul>

                    <!-- Conteúdo das tabs -->
                    <div class="tab-content" id="cursoTabsContent">
                        <div class="tab-pane fade show active" id="conteudo" role="tabpanel">
                            <section class="curso-profile">
                                <div class="container">
                                    <div class="curso-profile-card">
                                        <!-- Course Info and Teacher Info (Side by side) -->
                                        <div class="row justify-content-center mt-4">
                                            <div class="col-md-12 mb-5">
                                                <div class="curso-profile-card-infos shadow-none">
                                                    <div>
                                                        <img src="{{ asset('storage/' . $lesson->file->path) }}"
                                                            class="img-fluid curso-thumbnail"
                                                            alt="Curso de Cardiologia">
                                                    </div>
                                                    <div class="p-4">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-12">
                                                                <h3 class="titulo-curso mb-0">{{ $lesson->name }}</h3>
                                                            </div>
                                                        </div>
                                                        <h5>
                                                            <i class="bi bi-bookmark"></i>
                                                            {{ $lesson->specialty->name }}
                                                        </h5>
                                                        <div class="mt-3 text-muted">
                                                            {!! $lesson->description !!}
                                                        </div>
                                                        <div
                                                            class="fs-6 d-flex justify-content-start gap-4 small text-muted text-center mt-3">
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
                                                                {{ $lesson->students->count() }} Estudantes
                                                            </span>
                                                        </div>
                                                        <div class="faqs mt-5">
                                                            <h4 class="text-center mb-4">Tópicos da Aula</h4>
                                                            <div class="accordion" id="faqAccordion">
                                                                @foreach ($lesson->topics as $index => $topic)
                                                                    <div
                                                                        class="accordion-item mb-3 border-0 rounded-3 overflow-hidden">
                                                                        <h2 class="accordion-header">
                                                                            <button
                                                                                class="accordion-button collapsed shadow-none"
                                                                                type="button"
                                                                                data-bs-toggle="collapse"
                                                                                data-bs-target="#faq-{{ $topic->id }}">
                                                                                <i
                                                                                    class="fas fa-graduation-cap me-2"></i>
                                                                                {{ $index + 1 }}.
                                                                                {{ $topic->title }}
                                                                            </button>
                                                                        </h2>
                                                                        <div id="faq-{{ $topic->id }}"
                                                                            class="accordion-collapse collapse"
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
                                            <div class="col-md-12">
                                                <div class="teacher-info shadow-none">
                                                    <div class="teacher-thumbnail">
                                                        <img src="{{ asset('storage/' . $lesson->teacher->file->path) }}"
                                                            class="img-fluid" alt="Professor Jesse Pinkman">
                                                    </div>
                                                    <div class="p-4"> <!-- Add padding to the text part only -->
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
                        </div>
                        <div class="tab-pane fade" id="anexos" role="tabpanel">
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
                                                    <button class="accordion-button collapsed shadow-none"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#faqtopic-{{ $topic->id }}">
                                                        <i class="fas fa-graduation-cap me-2"></i>
                                                        {{ $index + 1 }}.
                                                        {{ $topic->title }}
                                                    </button>
                                                </h2>
                                                <div id="faqtopic-{{ $topic->id }}"
                                                    class="accordion-collapse collapse"
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
                                                                    <li
                                                                        class="attachment-item d-flex align-items-center p-3 mb-2 rounded">
                                                                        <i
                                                                            class="bi bi-file-earmark-text text-danger me-3 fs-4"></i>
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
                        </div>
                        <div class="tab-pane fade" id="duvidas" role="tabpanel">
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
                                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal"
                                        data-bs-target="#modalPergunta">
                                        <i class="bi bi-chat-dots me-2"></i>
                                        Faça sua pergunta
                                    </button>
                                </div>
                                <!-- Modal -->
                                <div class="modal fade" id="modalPergunta" tabindex="-1"
                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3 class="modal-title fs-5" id="exampleModalLabel">
                                                    A pergunta será respondida pelo(a) professor(a)</h3>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
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
                        </div>
                        <div class="tab-pane fade" id="avaliacao" role="tabpanel">
                            <section class="avaliacao-section">
                                <div class="container text-center pt-5 pb-5">
                                    @if (Gate::allows('finishedLesson', $lesson))
                                        <h2 class="mb-3">Você já finalizou a avaliação desta aula!</h2>
                                        <p class="mb-4">Seu certificado está disponível na aba Certificado.</p>
                                    @else
                                        <h2 class="mb-3">Quiz de Avaliação do Aprendizado</h2>
                                        <p class="mb-4">Clique no botão abaixo para iniciar o quiz.</p>

                                        <button type="button"
                                            class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2"
                                            data-bs-toggle="modal" data-bs-target="#quizModal"
                                            data-lesson-id="{{ $lesson->id ?? 'UNKNOWN_LESSON' }}">
                                            <i class="bi bi-play-circle-fill"></i> Iniciar Quiz
                                        </button>
                                    @endif
                                </div>
                            </section>
                            <section>
                                <div class="modal fade" id="quizModal" tabindex="-1"
                                    aria-labelledby="quizModalLabel" aria-hidden="true" data-bs-backdrop="static">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content quiz-modal-content">
                                            <div class="quiz-header">
                                                <div id="questionCounter" class="question-counter">Questão ... de ...
                                                </div>
                                                <div class="progress">
                                                    <div id="quizProgressBar" class="progress-bar" style="width: 0%">
                                                    </div>
                                                </div>
                                                <div id="quizTopic" class="quiz-topic">Carregando Tópico...</div>
                                                <button type="button" id="quizCloseBtn" class="btn-close"
                                                    data-bs-dismiss="modal" aria-label="Fechar"></button>
                                            </div>

                                            <div class="question-box">
                                                <h2 id="questionText" class="question-text">Carregando Pergunta...
                                                </h2>
                                            </div>

                                            <div class="quiz-feedback-message mb-3 text-center"
                                                id="quizFeedbackMessage" style="display: none;">
                                            </div>

                                            <div id="answerOptionsContainer" class="answer-options"></div>

                                            <div class="action-buttons">
                                                <button id="btnSubmitAnswer" class="btn-submit" disabled>
                                                    <i class="bi bi-check-circle-fill me-2"></i> Responder
                                                </button>
                                                <button id="btnNextQuestion" class="btn-next" disabled>
                                                    <span class="btn-text">Próxima</span>
                                                    <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                                                </button>
                                            </div>

                                            <div id="topicFailedVideoInfo" class="p-3" style="display: none;">
                                                <h4>Vídeo para Revisão:</h4>
                                                <p id="videoTitle"></p>
                                                <img id="videoThumbnail" src="" alt="Video Thumbnail"
                                                    style="max-width: 200px; display: none;">
                                                <p><a id="videoLink" href="#" target="_blank"
                                                        style="display: none;">Assistir vídeo</a></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                        <div class="tab-pane fade" id="certificado" role="tabpanel">
                            <section class="avaliacao-section">
                                <div class="container text-center pt-5 pb-5">
                                    <h2 class="mb-3">Certificado de Conclusão</h2>
                                    @if (Gate::allows('generateCertificate', $lesson))
                                        <a href="{{ route('web.certificates.generate', $lesson->id) }}"
                                            class="btn btn-primary btn-lg d-inline-flex align-items-center gap-2"
                                            target="_blank" rel="noopener">
                                            <i class="bi bi-patch-check"></i> Gerar Certificado
                                        </a>
                                    @else
                                        <p class="mb-4">
                                            Você poderá gerar o certificado assim que concluir o quiz.
                                        </p>
                                    @endif
                                </div>
                            </section>
                        </div>
                        <div class="tab-pane fade" id="feedback" role="tabpanel">
                            <section class="avaliacao-section">
                                <h2 class="text-center mb-4">Avaliação da Aula</h2>
                                <div id="avaliacao-wrapper">
                                    <p class="text-center">Contribua com a melhoria da qualidade desta aula. Aqui você pode sugerir melhorias e dar um feedback diretamenta ao professor. Essa avaliação ficará visível apenas para você e o professor.</p>
                                    @if ($feedback === null)
                                        <div id="avaliacao-status" class="mb-4"></div>
                                        <form id="avaliacao-form" data-lesson-id="{{ $lesson->id }}"
                                            class="avaliacao-form mx-auto">
                                            @csrf
                                            <div class="mb-4 text-center">
                                                <label class="form-label d-block">Dê a sua nota:</label>
                                                <div class="star-rating">
                                                    @for ($i = 5; $i >= 1; $i--)
                                                        <input type="radio" name="rating"
                                                            id="star{{ $i }}"
                                                            value="{{ $i }}">
                                                        <label for="star{{ $i }}">&#9733;</label>
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="mb-4">
                                                <label for="comentario" class="form-label">Comentário
                                                    (opcional)</label>
                                                <textarea class="form-control" name="comentario" id="comentario" rows="4"></textarea>
                                            </div>

                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary">Enviar
                                                    Avaliação</button>
                                            </div>
                                        </form>
                                    @else
                                        <div class="text-center">
                                            <p class="mb-2">Você avaliou esta aula com:</p>
                                            <div class="star-rating read-only mb-3">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <span
                                                        style="color: {{ $i <= $feedback->rating ? '#ffc107' : '#ccc' }}">&#9733;</span>
                                                @endfor
                                            </div>

                                            @if ($feedback->comentario)
                                                <div class="alert alert-light border text-muted">
                                                    <strong>Comentário:</strong><br>
                                                    {{ $feedback->comentario }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

