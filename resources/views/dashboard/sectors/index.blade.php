@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="git-pull-request" class="nav-icon me-2 icon-md"></i>
                Gerenciar Setores
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card shadow-sm mb-4 mt-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Setores do Hospital</h3>
                <div class="d-flex align-items-center">
                    <form method="GET" action="{{ route('dashboard.sectors.index') }}" class="me-2">
                        <div class="input-group">
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                                placeholder="Pesquisar setores..">
                            <button class="btn btn-primary" type="submit" title="Buscar">
                                <i data-feather="search" class="icon-xs"></i>
                            </button>
                        </div>
                    </form>
                    <a href="{{ route('dashboard.sectors.create') }}" class="btn btn-primary text-nowrap">
                        <i data-feather="plus" class="nav-icon icon-xs"></i>
                        <span class="ms-1">Novo Setor</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if ($sectors->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sectors as $sector)
                                    <tr>
                                        <td>
                                            <i class="bi bi-buildings me-2"></i> 
                                            {{ $sector->name }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('dashboard.sectors.edit', $sector->id) }}"
                                                class="btn btn-ghost btn-icon btn-sm rounded-circle me-2" title="Editar"
                                                data-bs-toggle="tooltip">
                                                <i data-feather="edit" class="icon-xs"></i>
                                            </a>
                                            <button type="button" class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                                onclick="confirmDelete('delete-item-{{ $sector->id }}')" title="Apagar"
                                                data-bs-toggle="tooltip">
                                                <i data-feather="trash-2" class="icon-xs"></i>
                                            </button>
                                            <form class="d-none" id="delete-item-{{ $sector->id }}" method="POST"
                                                action="{{ route('dashboard.sectors.destroy', $sector->id) }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i data-feather="alert-circle" class="icon-xl text-muted"></i>
                        <h4 class="text-muted mt-2">Nenhum setor encontrado.</h4>
                        <p>Clique em "Novo Setor" para começar.</p>
                    </div>
                @endif
            </div>
        </div>
        @if ($sectors->isNotEmpty() && $sectors->hasPages())
            <div class="card card-pagination shadow-sm mt-4">
                <div class="card-body">
                    {{ $sectors->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
