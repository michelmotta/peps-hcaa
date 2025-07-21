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

    @auth
        @if (auth()->user()?->subscriptions->contains('id', $lesson->id))
            @include('web.includes.class_dashboard')
        @else
            @include('web.includes.class_subscription')
        @endif
    @endauth

    @guest
        @include('web.includes.class_subscription')
    @endguest
@endsection
