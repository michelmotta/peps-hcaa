@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>{{ $lesson->name }}</h1>
            <div class="fs-6 d-flex justify-content-center gap-4 small text-muted text-center">
                <span>
                    <i class="bi bi-list-check me-1"></i>
                    {{ $lesson->topics->count() }} Tópicos
                </span>
                <span>
                    <i class="bi bi-award me-1"></i>
                    {{ $lesson->workload }} Horas
                </span>
                <span>
                    <i class="bi bi-mortarboard me-1"></i>
                    {{ $lesson->subscriptions->count() }} Estudantes
                </span>
            </div>
        </div>
    </section>
    @if (auth()->check() && auth()->user()->subscriptions->contains('id', $lesson->id))
        @include('web.includes.class_dashboard')
    @else
        <section class="curso-profile py-4 bg-light">
            <div class="container">
                <div class="curso-profile-card">
                    <div class="row justify-content-center mt-4">
                        <div class="col-md-8 mb-5">
                            <div class="curso-profile-card-infos">
                                <div class="image-container">
                                    <img src="{{ asset('storage/' . $lesson->file->path) }}"
                                        class="img-fluid curso-thumbnail" alt="Curso de Cardiologia">

                                    <form action="{{ route('web.subscribe', $lesson) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="cta-button">
                                            <i class="bi bi-play-circle-fill"></i>
                                            <span>Assistir</span>
                                        </button>
                                    </form>
                                </div>
                                <div class="p-4 content-below-image">
                                    {{-- Centered Title --}}
                                    <div class="row align-items-center mb-3">
                                        <div class="col-12 text-center">
                                            <h3 class="titulo-curso mb-0">{{ $lesson->name }}</h3>
                                        </div>
                                    </div>

                                    {{-- Centered Button Position --}}
                                    <div class="text-center mb-4">
                                        {{-- The button form has been moved to the image-container above --}}
                                    </div>

                                    {{-- Centered Stats --}}
                                    <div class="fs-6 d-flex justify-content-center gap-4 small text-muted text-center mt-3">
                                        <span>
                                            <i class="bi bi-list-check me-1"></i>
                                            {{ $lesson->topics->count() }} tópicos
                                        </span>
                                        <span>
                                            <i class="bi bi-award me-1"></i>
                                            {{ $lesson->workload }} Horas
                                        </span>
                                        <span>
                                            <i class="bi bi-mortarboard me-1"></i>
                                            {{ $lesson->subscriptions->count() }} Estudantes
                                        </span>
                                    </div>
                                    <div class="mt-4 text-muted custom-text-justify">
                                        {!! $lesson->description !!}
                                    </div>
                                    <div class="faqs mt-5">
                                        <h4 class="text-center mb-4" style="color: #133b6a;text-transform: uppercase;font-weight: 600;">Tópicos da Aula</h4>
                                        <div class="accordion" id="faqAccordion">
                                            @foreach ($lesson->topics as $index => $topic)
                                                <div class="accordion-item mb-3 border-0 rounded-3 overflow-hidden">
                                                    <h2 class="accordion-header">
                                                        <button class="accordion-button collapsed shadow-none"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#faq-{{ $topic->id }}">
                                                            <i class="fas fa-graduation-cap me-2"></i>
                                                            {{ $topic->title }}
                                                        </button>
                                                    </h2>
                                                    <div id="faq-{{ $topic->id }}" class="accordion-collapse collapse"
                                                        data-bs-parent="#faqAccordion">
                                                        <div class="accordion-body bg-light custom-text-justify">
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
                        <div class="col-md-4">
                            <div class="teacher-info">
                                <div class="teacher-thumbnail pt-3">
                                    @if ($lesson->teacher && $lesson->teacher->file)
                                        <img src="{{ asset('storage/' . $lesson->teacher->file->path) }}" class="img-fluid"
                                            style="object-fit: cover;">
                                    @else
                                        <img src="https://placehold.co/300x300/EBF4FF/7F9CF5?text={{ strtoupper(substr($lesson->teacher->name, 0, 1)) }}"
                                            class="rounded-circle img-fluid">
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h6 class="fw-bold text-center mb-0">{{ $lesson->teacher->name }}</h6>
                                    <p class="text-muted text-center mb-3">{{ $lesson->teacher->expertise }}</p>
                                    <div class="text-muted small custom-text-justify teacher-biography">
                                        {!! $lesson->teacher->biography !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection
