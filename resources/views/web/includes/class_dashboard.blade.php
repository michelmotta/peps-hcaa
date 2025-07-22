<section class="curso-tabs py-5 bg-light">
    <div class="container">
        <div class="row g-4 flex-lg-nowrap">
            <div class="col-12 col-lg-4">
                <div class="course-playlist">
                    @php
                        $totalTopics = count($lesson->topics);
                        $watchedCount = count($watchedTopicIds ?? []);
                        $progressPercent = $totalTopics > 0 ? round(($watchedCount / $totalTopics) * 100) : 0;
                    @endphp
                    <div class="playlist-header">
                        <h5 class="playlist-title">Tópicos da Aula</h5>
                        <div class="course-progress-bar">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $progressPercent }}%;"
                                    aria-valuenow="{{ $progressPercent }}"></div>
                            </div>
                            <small>{{ $progressPercent }}% concluído</small>
                        </div>
                    </div>
                    <div class="playlist-body">
                        @forelse ($lesson->topics as $index => $topic)
                            @php
                                $isWatched = in_array($topic->id, $watchedTopicIds ?? []);
                            @endphp
                            <div class="topic-item {{ $isWatched ? 'watched' : '' }}"
                                data-video="{{ asset('storage/' . $topic->video->path) }}"
                                data-topic-id="{{ $topic->id }}">
                                <div class="topic-visuals">
                                    <img src="{{ asset('storage/' . $topic->video->thumbnail_path) }}" alt="Thumb"
                                        class="topic-thumb">
                                    <div class="playing-indicator-overlay">
                                        <div class="equalizer-bar"></div>
                                        <div class="equalizer-bar"></div>
                                        <div class="equalizer-bar"></div>
                                    </div>
                                </div>

                                <div class="topic-info">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="topic-title">{{ $topic->title }}</h6>
                                    </div>
                                    <small class="topic-duration">
                                        <i class="bi bi-clock"></i>
                                        {{ humanAbbreviatedTime($topic->video->duration) }}
                                    </small>
                                </div>

                                <div class="topic-status">
                                    <span class="topic-number">{{ $index + 1 }}</span>
                                    <i class="bi bi-play-circle-fill play-icon"></i>
                                    <i class="bi bi-check-circle-fill watched-icon"></i>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <p>Nenhum tópico cadastrado para esta aula.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8 d-flex flex-column">
                <div class="bg-player">
                    <div class="video-container mb-4">
                        <div class="overflow-hidden shadow">
                            <video controls class="js-player player w-100"></video>
                        </div>
                    </div>
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
