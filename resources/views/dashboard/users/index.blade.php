@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="users" class="nav-icon me-2 icon-md"></i>
                Gerenciar Usuários
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card shadow-sm mb-4 mt-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Usuários</h3>
                <div class="d-flex align-items-center">
                    <form method="GET" action="{{ route('dashboard.users.index') }}" class="me-2">
                        <div class="input-group">
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                                placeholder="Pesquisar usuários...">
                            <button class="btn btn-primary" type="submit" title="Buscar">
                                <i data-feather="search" class="icon-xs"></i>
                            </button>
                        </div>
                    </form>
                    <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary text-nowrap">
                        <i data-feather="plus" class="nav-icon icon-xs"></i>
                        <span class="ms-1">Novo Usuário</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="row g-4">
            @forelse ($users as $user)
                <div class="col-lg-6">
                    <div class="card h-100 shadow-sm overflow-hidden">
                        <div class="row g-0 h-100">

                            <div
                                class="col-md-3 d-flex flex-column align-items-center justify-content-center p-3 {{ $user->active ? 'bg-success-soft' : 'bg-dark-soft' }}">
                                <a href="{{ asset('storage/' . $user->file->path) }}"
                                    data-fancybox="user-{{ $user->id }}">
                                    <img src="{{ asset('storage/' . $user->file->path) }}"
                                        alt="Avatar de {{ $user->name }}"
                                        class="img-fluid rounded-circle border border-2 border-white"
                                        style="width: 80px; height: 80px; object-fit: cover;">
                                </a>
                                <div class="mt-3 text-center">
                                    @if ($user->id !== Auth::id())
                                        <form id="activeForm-{{ $user->id }}"
                                            action="{{ route('dashboard.users.active', $user->id) }}" method="POST">
                                            @csrf
                                            <div class="form-check form-switch"
                                                title="{{ $user->active ? 'Desativar' : 'Ativar' }}">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    style="transform: scale(1.5);" {{ $user->active ? 'checked' : '' }}
                                                    onchange="this.form.submit()">
                                            </div>
                                            <small class="text-muted">{{ $user->active ? 'Ativo' : 'Inativo' }}</small>
                                        </form>
                                    @else
                                        <span class="badge bg-white text-dark border">Você</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="card-body d-flex flex-column h-100 py-4 pe-4">
                                    <div class="flex-grow-1">
                                        <h4 class="card-title fw-bold mb-1" style="font-size: 1.4rem;">{{ $user->name }}
                                        </h4>
                                        <p class="card-text small text-muted">{{ $user->expertise }}</p>
                                        <p class="card-text small text-muted mb-2">
                                            <a href="mailto:{{ $user->email }}"
                                                class="text-reset">{{ $user->email }}</a>
                                        </p>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($user->profiles as $profile)
                                                <span
                                                    class="badge badge-primary-soft fw-normal">{{ $profile->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="mt-4 pt-3 border-top">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <p class="small text-muted mb-0">
                                                    <i data-feather="calendar" class="icon-xs me-1"></i>
                                                    Usuário desde {{ $user->created_at->format('d/m/Y') }}
                                                </p>
                                            </div>
                                            <div class="col-6 text-end">
                                                <a href="#" class="btn btn-ghost btn-icon btn-sm rounded-circle"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#loginHistoryModal-{{ $user->id }}"
                                                    title="Histórico de Login" data-bs-toggle="tooltip">
                                                    <i data-feather="clock" class="icon-xs"></i>
                                                </a>
                                                <a href="{{ route('dashboard.users.edit', $user->id) }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar"
                                                    data-bs-toggle="tooltip">
                                                    <i data-feather="edit" class="icon-xs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="loginHistoryModal-{{ $user->id }}" tabindex="-1"
                    aria-labelledby="loginHistoryModalLabel-{{ $user->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="loginHistoryModalLabel-{{ $user->id }}">Últimos 5 Logins de
                                    {{ $user->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <ul class="list-group list-group-flush">
                                    @forelse($user->logins as $login)
                                        <li class="list-group-item d-flex justify-content-between">
                                            <span>{{ $login->created_at->format('d/m/Y \à\s H:i') }}</span>
                                            <span class="text-muted">{{ $login->ip_address }}</span>
                                        </li>
                                    @empty
                                        <li class="list-group-item text-center">Nenhum login registrado.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i data-feather="alert-circle" class="icon-xl text-muted"></i>
                            <h4 class="text-muted mt-2">Nenhum usuário encontrado</h4>
                            <p class="text-muted">Tente ajustar sua busca ou adicione um novo usuário.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        @if ($users->isNotEmpty() && $users->hasPages())
            <div class="card card-pagination shadow-sm mt-4">
                <div class="card-body">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
