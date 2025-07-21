@extends('templates.dashboard')

@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="bar-chart-2" class="nav-icon me-2 icon-md"></i>
                Análise de Feedbacks: {{ $lesson->name }}
            </h1>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card shadow-sm border-0 mb-4 mt-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <a href="{{ route('dashboard.lessons.index') }}" class="btn btn-outline-primary d-flex align-items-center">
                    <i data-feather="arrow-left" class="nav-icon me-2 icon-xs"></i>
                    Voltar
                </a>
                <form method="GET" action="{{-- route('dashboard.lessons.doubts.index', $lesson) --}}" class="mb-0" style="width: 300px;">
                    <div class="input-group">
                        <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                            placeholder="Pesquisar comentários...">
                        <button class="btn btn-primary" type="submit" title="Buscar"><i data-feather="search"
                                class="icon-xs"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ $totalFeedbacks }}</h4>
                                <p class="text-muted mb-0">Total de Avaliações</p>
                            </div>
                            <div class="icon-shape bg-light-primary text-primary rounded-circle"><i data-feather="users"
                                    class="icon-md"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ number_format($averageRating, 1) }} / 5</h4>
                                <p class="text-muted mb-0">Média Geral</p>
                            </div>
                            <div class="icon-shape bg-light-warning text-warning rounded-circle"><i data-feather="star"
                                    class="icon-md"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ number_format($positivePercentage, 0) }}%</h4>
                                <p class="text-muted mb-0">Sentimento Positivo</p>
                            </div>
                            <div class="icon-shape bg-light-success text-success rounded-circle"><i
                                    data-feather="trending-up" class="icon-md"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-0">{{ number_format($negativePercentage, 0) }}%</h4>
                                <p class="text-muted mb-0">Sentimento Negativo</p>
                            </div>
                            <div class="icon-shape bg-light-danger text-danger rounded-circle"><i
                                    data-feather="trending-down" class="icon-md"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Distribuição das Avaliações</h4>
            </div>
            <div class="card-body">
                @for ($i = 5; $i >= 1; $i--)
                    @php
                        $count = $ratingsCount[$i] ?? 0;
                        $percentage = $totalFeedbacks > 0 ? ($count / $totalFeedbacks) * 100 : 0;
                    @endphp
                    <div class="d-flex align-items-center mb-3">
                        <div class="rating-stars me-3"><i data-feather="star"
                                class="icon-xs @if ($i >= 1) filled @endif"></i><i data-feather="star"
                                class="icon-xs @if ($i >= 2) filled @endif"></i><i data-feather="star"
                                class="icon-xs @if ($i >= 3) filled @endif"></i><i data-feather="star"
                                class="icon-xs @if ($i >= 4) filled @endif"></i><i data-feather="star"
                                class="icon-xs @if ($i >= 5) filled @endif"></i></div>
                        <div class="progress flex-grow-1" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%;">
                            </div>
                        </div>
                        <span class="ms-3 text-muted fw-bold" style="width: 40px;">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        </div>

        <h3 class="mb-3">Feedbacks Recebidos</h3>
        @forelse ($feedbacks as $feedback)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex">
                            <img src="{{ asset('storage/' . $feedback->user->file->path) }}"
                                class="avatar rounded-circle me-3" alt="Avatar">
                            <div>
                                <strong class="d-block">{{ $feedback->user->name }}</strong>
                                <div class="rating-stars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i data-feather="star"
                                            class="icon-xs @if ($i <= $feedback->rating) filled @endif"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <small class="text-muted me-3">{{ $feedback->created_at->diffForHumans() }}</small>
                            <button type="button" class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                onclick="confirmDelete('delete-item-{{ $feedback->id }}')" title="Apagar">
                                <i data-feather="trash-2" class="icon-xs"></i>
                            </button>
                            <form class="d-none" id="delete-item-{{ $feedback->id }}" method="POST"
                                action="{{ route('dashboard.lessons.feedbacks.destroy', [$lesson, $feedback]) }}">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </div>
                    @if ($feedback->comentario)
                        <p class="mb-0 mt-3">{{ $feedback->comentario }}</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5"><i data-feather="inbox" class="icon-xl text-muted"></i>
                    <h4 class="text-muted mt-3">Nenhum feedback encontrado.</h4>
                </div>
            </div>
        @endforelse

        @if ($feedbacks->hasPages())
            <div class="mt-4">
                {{ $feedbacks->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
<style>
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
    }

    .rating-stars .feather-star.filled {
        color: #F5B800;
        fill: #F5B800;
    }
</style>
