@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="users" class="nav-icon me-2 icon-md"></i>
                Gerenciar Usuários
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="d-grid d-lg-block ms-auto text-end mb-5">
                    <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary btn-lg">
                        <i data-feather="plus" class="nav-icon me-2 icon-xs"></i>
                        Novo Usuário
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Usuários</h3>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('dashboard.users.index') }}">
                                    <input type="search" class="form-control w-100 " name="q" style="height: 50px"
                                        value="{{ request('q') }}" placeholder="Pesquisar usuários...">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table mb-0 text-nowrap table-centered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Nome</th>
                                        <th scope="col" class="text-center">E-mail</th>
                                        <th scope="col" class="text-center">Usuário</th>
                                        <th scope="col" class="text-center">Perfis</th>
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Ativar/Desativar</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <a href="{{ asset('storage/' . $user->file->path) }}" data-fancybox>
                                                            <img src="{{ asset('storage/' . $user->file->path) }}"
                                                                class="avatar-md avatar rounded-circle" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="ms-3 lh-1">
                                                        <h5 class="mb-0">{{ $user->name }}</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $user->email }}</td>
                                            <td class="text-center">{{ $user->username }}</td>
                                            <td class="text-center">
                                                @foreach ($user->profiles as $profile)
                                                    <span class="badge badge-primary-soft">
                                                        {{ $profile->name }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $user->active ? 'success' : 'secondary' }} ms-2">
                                                    {{ $user->active ? 'ATIVO' : 'INATIVO' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($user->id !== Auth::id())
                                                    <div class="d-flex justify-content-center align-items-center"
                                                        style="height: 100%;">
                                                        <form id="activeForm-{{ $user->id }}"
                                                            action="{{ route('dashboard.users.active', $user->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="form-check form-switch d-flex align-items-center">
                                                                <input class="form-check-input switch-large"
                                                                    style="transform: scale(1.5);" type="checkbox"
                                                                    role="switch" name="status"
                                                                    id="flexSwitchCheckChecked-{{ $user->id }}"
                                                                    {{ $user->active ? 'checked' : '' }}
                                                                    onchange="document.getElementById('activeForm-{{ $user->id }}').submit();">
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.users.edit', $user->id) }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip"
                                                    data-template="editTwo" title="Editar" data-bs-toggle="tooltip">
                                                    <i data-feather="edit" class="icon-xs"></i>
                                                    <div id="editTwo" class="d-none">
                                                        <span>Edit</span>
                                                    </div>
                                                </a>
                                                {{-- 
                                                @if ($user->id !== Auth::id())
                                                    <button type="button"
                                                        class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip text-danger"
                                                        data-template="trashOne"
                                                        onclick="confirmDelete('delete-item-{{ $user->id }}')"
                                                        title="Apagar" data-bs-toggle="tooltip">
                                                        <i data-feather="trash-2" class="icon-xs"></i>
                                                        <div id="trashOne" class="d-none">
                                                            <span>Delete</span>
                                                        </div>
                                                    </button>
                                                    <form class="d-none" id="delete-item-{{ $user->id }}"
                                                        method="POST"
                                                        action="{{ route('dashboard.users.destroy', $user->id) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                                --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
