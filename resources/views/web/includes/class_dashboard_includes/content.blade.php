<section class="curriculum-section">
    <div class="container">
        <h2 class="section-title"><span>Conteúdo da Aula</span></h2>
        <div class="accordion curriculum-accordion" id="curriculumAccordion">
            @forelse ($lesson->topics as $index => $topic)
                @php
                    $isWatched = in_array($topic->id, $watchedTopicIds);
                @endphp
                {{-- The topic-watched class is now applied directly to the item --}}
                <div class="accordion-item {{ $isWatched ? 'topic-watched' : '' }}">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#topic-collapse-{{ $topic->id }}">

                            {{-- Status icon remains the same --}}
                            <div class="topic-status-icon">
                                @if ($isWatched)
                                    <i class="bi bi-check-circle-fill"></i>
                                @else
                                    <i class="bi bi-circle"></i>
                                @endif
                            </div>

                            {{-- SEMANTIC FIX: Replaced H4 with divs for correct structure --}}
                            <div class="topic-title-content">
                                <span class="topic-number">Tópico {{ $index + 1 }}</span>
                                <div class="topic-main-title">{{ $topic->title }}</div>
                            </div>
                        </button>
                    </h2>
                    <div id="topic-collapse-{{ $topic->id }}" class="accordion-collapse collapse"
                        data-bs-parent="#curriculumAccordion">
                        <div class="accordion-body">
                            <div class="topic-description">
                                {!! $topic->description !!}
                            </div>

                            @if ($topic->attachments->isNotEmpty())
                                <div class="attachment-list">
                                    <h5 class="attachment-title"><i class="bi bi-paperclip"></i> Materiais
                                        Complementares</h5>
                                    <ul class="list-unstyled">
                                        @foreach ($topic->attachments as $attachment)
                                            {{-- UX FIX: The entire list item is now a downloadable link --}}
                                            <li>
                                                <a href="{{ Storage::url($attachment['path']) }}"
                                                    class="attachment-item" download>
                                                    <div class="attachment-info">
                                                        <i class="bi bi-file-earmark-text text-primary"></i>
                                                        <div>
                                                            <strong>{{ $attachment['name'] }}</strong>
                                                            <small class="d-block text-muted">Enviado em
                                                                {{ $attachment['date'] }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="download-btn">
                                                        <i class="bi bi-download"></i> Baixar
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-journal-x display-4 text-muted"></i>
                    <h4 class="mt-3">Nenhum tópico cadastrado</h4>
                    <p class="text-muted">O conteúdo desta aula será adicionado em breve.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
