@php
    use Illuminate\Support\Str;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="mb-5 p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="message-circle" class="nav-icon me-2 icon-md"></i>
                Gerenciar Sugestões
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="d-grid d-lg-block ms-auto text-end mb-5">
                    <a href="{{ route('dashboard.suggestions.create') }}" class="btn btn-primary btn-lg">
                        <i data-feather="plus" class="nav-icon me-2 icon-xs"></i>
                        Nova Sugestão
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Lista de sugestões</h3>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('dashboard.suggestions.index') }}">
                                    <input type="search" class="form-control w-100" name="q" style="height: 50px"
                                        value="{{ request('q') }}" placeholder="Pesquisar sugestões...">
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
                                        <th scope="col" class="text-center">Votos</th>
                                        <th scope="col" class="text-center">Porcentagem de Votos</th>
                                        <th scope="col" class="text-center">Autor</th>
                                        <th scope="col" class="text-center">Publicação</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($suggestions as $suggestion)
                                        <tr>
                                            <td>
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#modal-{{ $suggestion->id }}">{{ $suggestion->name }}</a>
                                            </td>
                                            <td class="text-center">{{ $suggestion->votes }}</td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-auto" style="height: 6px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                            style="width: {{ number_format(($suggestion->votes / $totalVotes) * 100, 1) }}%;"
                                                            aria-valuenow="{{ number_format(($suggestion->votes / $totalVotes) * 100, 1) }}"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <div class="ms-2">
                                                        <span>{{ number_format(($suggestion->votes / $totalVotes) * 100, 1) }}%</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-primary-soft">{{ $suggestion->user->name }}</span>
                                            </td>
                                            <td class="text-center">{{ $suggestion->created_at_formatted }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.suggestions.edit', $suggestion) }}"
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
                                                    onclick="confirmDelete('delete-item-{{ $suggestion->id }}')" title="Apagar" data-bs-toggle="tooltip">
                                                    <i data-feather="trash-2" class="icon-xs"></i>
                                                    <div id="trashOne" class="d-none">
                                                        <span>Delete</span>
                                                    </div>
                                                </button>
                                                <form class="d-none" id="delete-item-{{ $suggestion->id }}" method="POST"
                                                    action="{{ route('dashboard.suggestions.destroy', $suggestion) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                        <div class="modal fade" id="modal-{{ $suggestion->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalCenterTitle">
                                                            Sugestão de {{ $suggestion->user->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h3 class="text-center mb-5">{{ $suggestion->name }}</h3>
                                                        <div style="font-size: 16">
                                                            {!! $suggestion->description !!}
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
                        {{ $suggestions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
