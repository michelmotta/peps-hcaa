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
                            @include('web.includes.class_dashboard_includes.informations')
                        </div>
                        <div class="tab-pane fade" id="anexos" role="tabpanel">
                            @include('web.includes.class_dashboard_includes.content')
                        </div>
                        <div class="tab-pane fade" id="duvidas" role="tabpanel">
                            @include('web.includes.class_dashboard_includes.doubts')
                        </div>
                        <div class="tab-pane fade" id="avaliacao" role="tabpanel">
                            @include('web.includes.class_dashboard_includes.quiz')
                        </div>
                        <div class="tab-pane fade" id="certificado" role="tabpanel">
                            @include('web.includes.class_dashboard_includes.certificate')
                        </div>
                        <div class="tab-pane fade" id="feedback" role="tabpanel">
                            @include('web.includes.class_dashboard_includes.feedback')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
