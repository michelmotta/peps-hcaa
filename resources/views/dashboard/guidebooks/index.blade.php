@php
    use Illuminate\Support\Str;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="info" class="nav-icon me-2 icon-md"></i>
                Gerenciar Manuais
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="d-grid d-lg-block ms-auto text-end mb-5">
                    <a href="{{ route('dashboard.guidebooks.create') }}" class="btn btn-primary btn-lg">
                        <i data-feather="plus" class="nav-icon me-2 icon-xs"></i>
                        Novo Manual
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Manuais</h3>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('dashboard.guidebooks.index') }}">
                                    <input type="search" class="form-control w-100" name="q" style="height: 50px"
                                        value="{{ request('q') }}" placeholder="Pesquisar manuais...">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table mb-0 text-nowrap table-centered">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Título</th>
                                        <th scope="col">Descrição</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($guidebooks as $guidebook)
                                        <tr>
                                            <td>{{ $guidebook->title }}</td>
                                            <td>{!! Str::limit(strip_tags($guidebook->description), 80) !!}</td>
                                            <td class="text-center">
                                                <button data-bs-toggle="modal" data-bs-target="#modal-{{ $guidebook->id }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip"
                                                    title="Visualizar" data-bs-toggle="tooltip">
                                                    <i data-feather="eye" class="nav-icon icon-xs"></i>
                                                </button>
                                                <a href="{{ route('dashboard.guidebooks.edit', $guidebook->id) }}"
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
                                                    onclick="confirmDelete('delete-item-{{ $guidebook->id }}')"
                                                    title="Apagar" data-bs-toggle="tooltip">
                                                    <i data-feather="trash-2" class="icon-xs"></i>
                                                    <div id="trashOne" class="d-none">
                                                        <span>Delete</span>
                                                    </div>
                                                </button>
                                                <form class="d-none" id="delete-item-{{ $guidebook->id }}" method="POST"
                                                    action="{{ route('dashboard.guidebooks.destroy', $guidebook->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-{{ $guidebook->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalCenterTitle">
                                                            Manual
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h3 class="text-center mb-5">{{ $guidebook->title }}</h3>
                                                        <div class="manual-body">
                                                            {!! $guidebook->description !!}
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
                        {{ $guidebooks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
