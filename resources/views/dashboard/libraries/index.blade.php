@php
    use Illuminate\Support\Str;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="bookmark" class="nav-icon me-2 icon-md"></i>
                Gerenciar Biblioteca
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="d-grid d-lg-block ms-auto text-end mb-5">
                    <a href="{{ route('dashboard.libraries.create') }}" class="btn btn-primary btn-lg">
                        <i data-feather="plus" class="nav-icon me-2 icon-xs"></i>
                        Novo Arquivo
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Arquivos</h3>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('dashboard.libraries.index') }}">
                                    <input type="search" class="form-control w-100" name="q" style="height: 50px"
                                        value="{{ request('q') }}" placeholder="Pesquisar arquivos...">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table mb-0 text-nowrap table-centered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Título</th>
                                        <th>Arquivo</th>
                                        <th class="text-center">Tipo</th>
                                        <th class="text-center">Tamanho</th>
                                        <th class="text-center">Cadastrado Por</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($libraries as $library)
                                        <tr>
                                            <td>
                                                <i data-feather="file" class="icon-xs me-3"></i>
                                                {{ $library->title }}
                                            </td>
                                            <td>{{ $library->file->name }}</td>
                                            <td class="text-center">{{ $library->file->extension }}</td>
                                            <td class="text-center">{{ $library->file->size_in_mb }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-primary-soft">{{ $library->user->name }}</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ asset('storage/' . $library->file->path) }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip"
                                                    data-fancybox>
                                                    <i data-feather="eye" class="nav-icon icon-xs"></i>
                                                </a>
                                                <a href="{{ route('dashboard.libraries.edit', $library->id) }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip"
                                                    data-template="editTwo" title="Editar" data-bs-toggle="tooltip">
                                                    <i data-feather="edit" class="icon-xs"></i>
                                                    <div id="editTwo" class="d-none"><span>Edit</span></div>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip text-danger"
                                                    data-template="trashOne"
                                                    onclick="confirmDelete('delete-item-{{ $library->id }}')"
                                                    title="Apagar" data-bs-toggle="tooltip">
                                                    <i data-feather="trash-2" class="icon-xs"></i>
                                                    <div id="trashOne" class="d-none"><span>Delete</span></div>
                                                </button>
                                                <form class="d-none" id="delete-item-{{ $library->id }}" method="POST"
                                                    action="{{ route('dashboard.libraries.destroy', $library->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $libraries->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
