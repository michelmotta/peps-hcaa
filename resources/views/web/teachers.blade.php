@extends('templates.web')

@section('content')
    <section>
        <div class="content-title">
            <h1>Professores</h1>
            <p class="sub-title">Conheça nossos professores</p>
        </div>
    </section>

    @include('web.includes.search_form', [
        'action' => route('web.teachers'),
        'title' => 'Pesquisar professores...',
    ])

    <section class="teachers-section">
        <div class="container">
            <div class="row">
                @forelse ($teachers as $teacher)
                    <div class="col-md-6 mb-4">
                        <div class="teacher-card-horizontal">
                            <div class="teacher-photo-wrapper">
                                <img src="{{ asset('storage/' . $teacher->file->path) }}" alt="{{ $teacher->name }}">
                            </div>

                            <div class="teacher-content-wrapper">
                                <div class="teacher-main-info">
                                    <h4 class="teacher-name">{{ $teacher->name }}</h4>
                                    <p class="teacher-expertise">{{ $teacher->expertise }}</p>
                                    <p class="teacher-bio">
                                        {{ Str::words($teacher->biography, 30, '...') }}
                                    </p>
                                </div>

                                <div class="teacher-footer-info">
                                    <div class="teacher-stats">
                                        <span><i class="bi bi-mortarboard"></i>
                                            {{ $teacher->student_subscriptions_count ?? 0 }} Estudantes</span>
                                        <span><i class="bi bi-journal-text"></i> {{ $teacher->created_lessons_count ?? 0 }}
                                            Aulas</span>
                                    </div>
                                    <a href="{{ route('web.teacher', $teacher->id) }}" class="teacher-cta-btn">
                                        Ver Perfil
                                    </a>
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

            <div class="pagination-wrapper">
                {{ $teachers->links() }}
            </div>
        </div>
    </section>
@endsection
