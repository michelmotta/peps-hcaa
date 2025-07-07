@php
    use Illuminate\Support\Str;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="message-circle" class="nav-icon me-2 icon-md"></i>
                Dúvidas: {{ $lesson->name }}
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('dashboard.lessons.index') }}" class="btn btn-primary btn-md mb-5">
                    <i data-feather="arrow-left" class="nav-icon me-2 icon-xs"></i>
                    Voltar
                </a>
                <div class="d-grid d-lg-block ms-auto text-end mb-5">
                    <a href="{{ route('dashboard.lessons.doubts.create', $lesson) }}" class="btn btn-primary btn-lg">
                        <i data-feather="plus" class="nav-icon me-2 icon-xs"></i>
                        Nova Dúvida
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Lista de dúvidas: {{ $lesson->name }}</h3>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('dashboard.lessons.doubts.index', $lesson) }}">
                                    <input type="search" class="form-control w-100" name="q" style="height: 50px"
                                        value="{{ request('q') }}" placeholder="Pesquisar dúvidas...">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table mb-0 text-nowrap table-centered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Estudante</th>
                                        <th scope="col">Pergunta</th>
                                        <th scope="col" class="text-center">Respondida?</th>
                                        <th scope="col" class="text-center">Respondida em</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($doubts as $doubt)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <a href="{{ asset('storage/' . $doubt->user->file->path) }}" data-fancybox>
                                                            <img src="{{ asset('storage/' . $doubt->user->file->path) }}"
                                                                class="avatar-md avatar rounded-circle" alt="">
                                                        </a>
                                                    </div>
                                                    <div class="ms-3 lh-1">
                                                        <h5 class="mb-0">{{ $doubt->user->name }}</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $doubt->doubt }}</td>
                                            <td class="text-center">
                                                @if($doubt->answered)
                                                    <span class="badge bg-success">Sim</span>
                                                @else
                                                    <span class="badge bg-danger">Não</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $doubt->answered_at_formatted }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.lessons.doubts.edit', [$lesson, $doubt]) }}"
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
                                                    onclick="confirmDelete('delete-item-{{ $doubt->id }}')" title="Apagar" data-bs-toggle="tooltip">
                                                    <i data-feather="trash-2" class="icon-xs"></i>
                                                    <div id="trashOne" class="d-none">
                                                        <span>Delete</span>
                                                    </div>
                                                </button>
                                                <form class="d-none" id="delete-item-{{ $doubt->id }}" method="POST"
                                                    action="{{ route('dashboard.lessons.doubts.destroy', [$lesson, $doubt]) }}">
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
                        {{ $doubts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
