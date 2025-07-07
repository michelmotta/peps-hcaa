@php
    use Illuminate\Support\Str;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="info" class="nav-icon me-2 icon-md"></i>
                Gerenciar Informações
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="d-grid d-lg-block ms-auto text-end mb-5">
                    <a href="{{ route('dashboard.information.create') }}" class="btn btn-primary btn-lg">
                        <i data-feather="plus" class="nav-icon me-2 icon-xs"></i>
                        Nova Informação
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Informações</h3>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('dashboard.users.index') }}">
                                    <input type="search" class="form-control w-100" name="q" style="height: 50px"
                                        value="{{ request('q') }}" placeholder="Pesquisar informações...">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table mb-0 text-nowrap table-centered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Título</th>
                                        <th scope="col">Descrição</th>
                                        <th scope="col" class="text-center">Publicado</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($information as $info)
                                        <tr>
                                            <td>{{ $info->title }}</td>
                                            <td>{!! Str::limit(strip_tags($info->description), 80) !!}</td>
                                            <td class="text-center">
                                                @if ($info->published)
                                                    <span class="badge badge-success-soft">Sim</span>
                                                @else
                                                    <span class="badge badge-secondary-soft">Não</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button data-bs-toggle="modal" data-bs-target="#modal-{{ $info->id }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip"
                                                    title="Visualizar" data-bs-toggle="tooltip">
                                                    <i data-feather="eye" class="nav-icon icon-xs"></i>
                                                </button>
                                                <a href="{{ route('dashboard.information.edit', $info->id) }}"
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
                                                    onclick="confirmDelete('delete-item-{{ $info->id }}')" title="Apagar" data-bs-toggle="tooltip">
                                                    <i data-feather="trash-2" class="icon-xs"></i>
                                                    <div id="trashOne" class="d-none">
                                                        <span>Delete</span>
                                                    </div>
                                                </button>
                                                <form class="d-none" id="delete-item-{{ $info->id }}" method="POST"
                                                    action="{{ route('dashboard.information.destroy', $info->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-{{ $info->id }}" tabindex="-1" role="dialog"
                                            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalCenterTitle">
                                                            Informação
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h3 class="text-center mb-5">{{ $info->title }}</h3>
                                                        <div style="font-size: 16">
                                                            {!! $info->description !!}
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Fechar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ $information->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
