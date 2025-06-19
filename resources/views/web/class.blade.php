@extends('templates.web')
@section('content')
    <section>
        <div class="content-title">
            <h1>{{ $lesson->name }}</h1>
            <div class="fs-6 d-flex justify-content-center gap-4 small text-muted text-center">
                <span>
                    <i class="bi bi-bookmark"></i>
                    {{ $lesson->specialty->name }}
                </span>
                <span>
                    <i class="bi bi-clock me-1"></i>
                    {{ $lesson->workload }} Horas
                </span>
                <span>
                    <i class="bi bi-list-check me-1"></i>
                    {{ $lesson->topics->count() }} Tópicos
                </span>
                <span>
                    <i class="bi bi-mortarboard me-1"></i>
                    {{ $lesson->students->count() }} Estudantes
                </span>
            </div>
        </div>
    </section>

    @auth
        @if ($user && $user->subscribedLessons->contains($lesson->id))
            @include('web.includes.class_dashboard') {{-- already subscribed --}}
        @else
            @include('web.includes.class_subscription') {{-- not subscribed yet --}}
        @endif
    @endauth

    @guest
        @include('web.includes.class_subscription') {{-- show subscribe prompt to guests --}}
    @endguest
@endsection
