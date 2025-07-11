@php
    use Illuminate\Support\Str;
@endphp

@extends('templates.dashboard')
@section('content')
    {{-- Page Header --}}
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="message-circle" class="nav-icon me-2 icon-md"></i>
                Gerenciar Sugestões
            </h1>
        </div>
    </div>

    <div class="container-fluid">
        {{-- Header with Title and Actions --}}
        <div class="card shadow-sm mb-4 mt-4">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
                <h3 class="mb-0">Sugestões</h3>
                <div class="d-flex align-items-center">
                    <form method="GET" action="{{ route('dashboard.suggestions.index') }}" class="me-2">
                        <div class="input-group">
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                                placeholder="Pesquisar sugestões...">
                            <button class="btn btn-primary" type="submit" title="Buscar">
                                <i data-feather="search" class="icon-xs"></i>
                            </button>
                        </div>
                    </form>
                    <a href="{{ route('dashboard.suggestions.create') }}" class="btn btn-primary text-nowrap">
                        <i data-feather="plus" class="nav-icon icon-xs"></i>
                        <span class="ms-1">Nova Sugestão</span>
                    </a>
                </div>
            </div>
        </div>


        {{-- Feed/Timeline Layout --}}
        <div class="row justify-content-center">
            <div class="col-lg-10">
                @forelse ($suggestions as $suggestion)
                    <div class="card shadow-sm mb-4">
                        {{-- START: Updated Post Header with Rank on the Right --}}
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            {{-- Left side: Avatar and user info --}}
                            <div class="d-flex align-items-center">
                                @if ($suggestion->user && $suggestion->user->file)
                                    <a href="{{ asset('storage/' . $suggestion->user->file->path) }}" data-fancybox
                                        class="me-3">
                                        <img src="{{ asset('storage/' . $suggestion->user->file->path) }}"
                                            class="rounded-circle" alt="Avatar de {{ $suggestion->user->name }}"
                                            width="40" height="40" style="object-fit: cover;">
                                    </a>
                                @else
                                    <img src="https://placehold.co/40x40/EBF4FF/7F9CF5?text={{ strtoupper(substr($suggestion->user->name, 0, 1)) }}"
                                        alt="{{ $suggestion->user->name }}" class="rounded-circle me-3">
                                @endif
                                <div>
                                    <h5 class="mb-0">{{ $suggestion->user->name }}</h5>
                                    <small class="text-muted">sugerido em {{ $suggestion->created_at_formatted }}</small>
                                </div>
                            </div>

                            {{-- Right side: Prominent Rank Badge --}}
                            <div>
                                @php
                                    $rank = $startRank + $loop->iteration;
                                    $rankClass = 'rank-other';
                                    if ($rank == 1) {
                                        $rankClass = 'rank-1';
                                    }
                                    if ($rank == 2) {
                                        $rankClass = 'rank-2';
                                    }
                                    if ($rank == 3) {
                                        $rankClass = 'rank-3';
                                    }
                                @endphp
                                <div class="rank-badge {{ $rankClass }}">#{{ $rank }}</div>
                            </div>
                        </div>
                        {{-- END: Updated Post Header --}}


                        {{-- Post Body --}}
                        <div class="card-body">
                            <h3 class="mb-3">{{ $suggestion->name }}</h3>
                            <p class="text-muted">
                                {{ Str::limit(strip_tags($suggestion->description), 200) }}
                            </p>
                            <div class="mt-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-bold">{{ $suggestion->votes }} Votos</span>
                                    @if ($totalVotes > 0)
                                        <span
                                            class="text-muted">{{ number_format(($suggestion->votes / $totalVotes) * 100, 1) }}%</span>
                                    @endif
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ $totalVotes > 0 ? ($suggestion->votes / $totalVotes) * 100 : 0 }}%;"
                                        aria-valuenow="{{ $suggestion->votes }}" aria-valuemin="0"
                                        aria-valuemax="{{ $totalVotes }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Post Footer --}}
                        <div class="card-footer bg-white text-end">
                            <button data-bs-toggle="modal" data-bs-target="#modal-{{ $suggestion->id }}"
                                class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Visualizar">
                                <i data-feather="eye" class="icon-xs"></i>
                            </button>
                            @can('isCoordenador')
                                <a href="{{ route('dashboard.suggestions.edit', $suggestion) }}"
                                    class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar">
                                    <i data-feather="edit" class="icon-xs"></i>
                                </a>
                                <button type="button" class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                    onclick="confirmDelete('delete-item-{{ $suggestion->id }}')" title="Apagar">
                                    <i data-feather="trash-2" class="icon-xs"></i>
                                </button>
                                <form class="d-none" id="delete-item-{{ $suggestion->id }}" method="POST"
                                    action="{{ route('dashboard.suggestions.destroy', $suggestion) }}">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endcan
                        </div>
                    </div>

                    {{-- Modal --}}
                    <div class="modal fade" id="modal-{{ $suggestion->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="modal-title-{{ $suggestion->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modal-title-{{ $suggestion->id }}">Sugestão de
                                        {{ $suggestion->user->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <h3 class="text-center mb-5">{{ $suggestion->name }}</h3>
                                    <div style="font-size: 16px;">{!! $suggestion->description !!}</div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i data-feather="inbox" class="icon-lg text-muted mb-3"></i>
                            <h4 class="text-muted">Nenhuma sugestão encontrada</h4>
                            <p class="text-muted mb-0">Seja o primeiro a criar uma nova sugestão!</p>
                        </div>
                    </div>
                @endforelse

                {{-- Pagination --}}
                @if ($suggestions->isNotEmpty() && $suggestions->hasPages())
                    <div class="card card-pagination shadow-sm mt-4">
                        <div class="card-body">
                            {{ $suggestions->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
