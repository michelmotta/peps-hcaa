@extends('templates.web')

@section('content')
    <section>
        <div class="content-title">
            <h1>Professor</h1>
            <p class="sub-title">Conheça mais sobre o especialista</p>
        </div>
    </section>
    <section class="teacher-profile-section">
        <div class="container">
            <div class="profile-card">
                <div class="profile-intro">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <div class="intro-identity">
                                <img src="{{ asset('storage/' . $teacher->file->path) }}" class="intro-photo"
                                    alt="{{ $teacher->name }}">
                                <h1 class="intro-name">{{ $teacher->name }}</h1>
                                <p class="intro-expertise">{{ $teacher->expertise }}</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="intro-stats">
                                <div class="stat-item">
                                    <i class="bi bi-mortarboard"></i>
                                    <strong>{{ $teacher->student_subscriptions_count ?? 0 }}</strong>
                                    <span>Estudantes</span>
                                </div>
                                <div class="stat-item">
                                    <i class="bi bi-journal-text"></i>
                                    <strong>{{ $teacher->created_lessons_count ?? 0 }}</strong>
                                    <span>Aulas Ministradas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="profile-divider">
                <div class="biography-wrapper">
                    <h3 class="section-title">Sobre o professor</h3>
                    <p>{{ $teacher->biography }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
