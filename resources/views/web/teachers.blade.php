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
    <section class="teachers-cards">
        <div class="container">
            <div class="row">
                @foreach ($teachers as $teacher)
                    <div class="col-md-3">
                        <div class="teacher-card">
                            <div class="teacher-card-thumbnail">
                                <img src="{{ asset('storage/' . $teacher->file->path) }}"
                                    class="img-fluid">
                            </div>
                            <div class="teacher-card-infos">
                                <h4>{{ $teacher->name }}</h4>
                                <p>{{ $teacher->expertise }}</p>
                                <hr>
                                <div class="meta-info">
                                    <span><i class="bi bi-mortarboard"></i> {{ $teacher->createdLessons->flatMap->students->unique('id')->count() }} Estudantes</span>
                                    <span><i class="bi bi-journal-text"></i> {{ $teacher->createdLessons->count() }} Aulas</span>
                                </div>
                                <a href="{{ route('web.teacher', $teacher->id) }}" class="read-more-btn">
                                    <i class="bi bi-person-vcard me-2"></i> Ver Professor
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <div class="pagination-wrapper">
        {{ $teachers->links() }}
    </div>
@endsection
