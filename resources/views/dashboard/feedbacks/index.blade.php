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
                <form method="GET" action="{{ route('dashboard.lessons.messages.index', $lesson) }}" class="mb-0"
                    style="width: 300px;">
                    <div class="input-group">
                        <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                            placeholder="Pesquisar comentários...">
                        <button class="btn btn-primary" type="submit" title="Buscar"><i data-feather="search"
                                class="icon-xs"></i></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="row justify-content-center">
            <!-- Card: Total de Avaliações -->
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-start border-primary border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="text-muted text-uppercase small mb-1">Total de Avaliações</p>
                                <h4 class="fw-bold mb-0">{{ $totalFeedbacks }}</h4>
                            </div>
                            <div class="col-auto">
                                <div class="icon-shape bg-light-primary text-primary rounded-circle">
                                    <i data-feather="users" class="icon-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-start border-warning border-4 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="text-muted text-uppercase small mb-1">Média Geral</p>
                                <div class="d-flex align-items-center">
                                    <h4 class="fw-bold mb-0 me-2">{{ number_format($averageRating, 1) }}</h4>
                                    <div class="text-warning">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= round($averageRating))
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="icon-shape bg-light-warning text-warning rounded-circle">
                                    <i data-feather="star" class="icon-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
