@extends('templates.dashboard')
@section('content')
    {{-- Page Header --}}
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="users" class="nav-icon me-2 icon-md"></i>
                Gerenciar Usuários
            </h1>
        </div>
    </div>

    <div class="container-fluid">
        {{-- Standalone Card for Actions and Search --}}
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

        {{-- Grid of User Cards --}}
        <div class="row g-4">
            @forelse ($users as $user)
                {{-- User Card Column --}}
                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                    {{-- ✅ Added position-relative to the card --}}
                    <div class="card h-100 shadow-sm position-relative">

                        {{-- ✅ Moved the toggle form here and positioned it --}}
                        @if ($user->id !== Auth::id())
                            <form id="activeForm-{{ $user->id }}"
                                action="{{ route('dashboard.users.active', $user->id) }}" method="POST"
                                class="position-absolute top-0 end-0 p-3"
                                title="{{ $user->active ? 'Desativar' : 'Ativar' }}">
                                @csrf
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        style="transform: scale(1.5);" {{ $user->active ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                </div>
                            </form>
                        @endif

                        <div class="card-body text-center d-flex flex-column">
                            {{-- Avatar --}}
                            <div class="mb-3">
                                <a href="{{ asset('storage/' . $user->file->path) }}" data-fancybox>
                                    <img src="{{ asset('storage/' . $user->file->path) }}" class="avatar-xl rounded-circle"
                                        alt="Avatar de {{ $user->name }}">
                                </a>
                            </div>

                            {{-- User Info --}}
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <p class="text-muted small mb-2">{{ $user->email }}</p>

                            {{-- Status Badge --}}
                            <div>
                                @if ($user->active)
                                    <span class="badge bg-success-soft text-success mb-3">Ativo</span>
                                @else
                                    <span class="badge bg-danger-soft text-danger mb-3">Inativo</span>
                                @endif
                            </div>

                            {{-- Profiles/Roles --}}
                            <div class="mb-3">
                                @foreach ($user->profiles as $profile)
                                    <span class="badge badge-primary-soft">{{ $profile->name }}</span>
                                @endforeach
                            </div>

                            {{-- Spacer to push actions to the bottom --}}
                            <div class="flex-grow-1"></div>

                            {{-- Actions Footer --}}
                            <div class="d-flex justify-content-center align-items-center pt-3 border-top">
                                {{-- The edit button is now centered by itself --}}
                                <a href="{{ route('dashboard.users.edit', $user->id) }}"
                                    class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar"
                                    data-bs-toggle="tooltip">
                                    <i data-feather="edit" class="icon-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State Message --}}
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
        {{-- Pagination --}}
        @if ($users->isNotEmpty() && $users->hasPages())
            <div class="card card-pagination shadow-sm mt-4">
                <div class="card-body">
                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
