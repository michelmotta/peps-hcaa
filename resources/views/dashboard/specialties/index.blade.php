@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="bookmark" class="nav-icon me-2 icon-md"></i>
                Gerenciar Especialidades
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="d-grid d-lg-block ms-auto text-end mb-5">
                    <a href="{{ route('dashboard.specialties.create') }}" class="btn btn-primary btn-lg">
                        <i data-feather="plus" class="nav-icon me-2 icon-xs"></i>
                        Nova Especialidade
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Especialidades</h3>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('dashboard.specialties.index') }}">
                                    <input type="search" class="form-control w-100" name="q" style="height: 50px"
                                        value="{{ request('q') }}" placeholder="Pesquisar especialidades...">
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
                                        <th scope="col" class="text-center">Imagem</th>
                                        <th scope="col" class="text-center">Subespecialidades</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($specialties as $specialty)
                                        <tr>
                                            <td>
                                                <h5 class="mb-0">{{ $specialty->name }}</h5>
                                            </td>
                                            <td class="text-center">
                                                @if ($specialty->file)
                                                    <a href="{{ asset('storage/' . $specialty->file->path) }}"
                                                        data-fancybox>
                                                        <img class="avatar avatar-sm logo_img"
                                                            src="{{ asset('storage/' . $specialty->file->path) }}"
                                                            alt="">
                                                    </a>
                                                @else
                                                    <span class="text-muted">Sem imagem</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($specialty->children->isNotEmpty())
                                                    @foreach ($specialty->children as $sub)
                                                        <span class="badge badge-secondary-soft">{{ $sub->name }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Sem subespecialidades</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.specialties.edit', $specialty->id) }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip"
                                                    data-template="editTwo" title="Editar" data-bs-toggle="tooltip">
                                                    <i data-feather="edit" class="icon-xs"></i>
                                                    <div id="editTwo" class="d-none">
                                                        <span>Edit</span>
                                                    </div>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip text-danger"
                                                    data-template="trashOne"
                                                    onclick="confirmDelete('delete-item-{{ $specialty->id }}')"
                                                    title="Apagar" data-bs-toggle="tooltip">
                                                    <i data-feather="trash-2" class="icon-xs"></i>
                                                    <div id="trashOne" class="d-none">
                                                        <span>Delete</span>
                                                    </div>
                                                </button>
                                                <form class="d-none" id="delete-item-{{ $specialty->id }}" method="POST"
                                                    action="{{ route('dashboard.specialties.destroy', $specialty->id) }}">
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
                    <div class="card-footer">
                        {{ $specialties->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
