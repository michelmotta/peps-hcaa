@php
    use Illuminate\Support\Str;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="message-circle" class="nav-icon me-2 icon-md"></i>
                Comunicados: {{ $lesson->name }}
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card shadow-sm border-0 mb-4 mt-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('dashboard.lessons.index') }}"
                        class="btn btn-outline-primary d-flex align-items-center">
                        <i data-feather="arrow-left" class="nav-icon me-2 icon-xs"></i>
                        Voltar
                    </a>
                    <h3 class="mb-0">Comunicados</h3>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="{{ route('dashboard.lessons.messages.index', $lesson) }}" class="mb-0">
                        <div class="input-group">
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                                placeholder="Pesquisar comunicados...">
                            <button class="btn btn-primary" type="submit" title="Buscar"><i data-feather="search"
                                    class="icon-xs"></i></button>
                        </div>
                    </form>
                    <a href="{{ route('dashboard.lessons.messages.create', $lesson) }}" class="btn btn-primary text-nowrap">
                        <i data-feather="plus" class="nav-icon icon-xs"></i>
                        <span class="ms-1">Novo Comunicado</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="table-responsive table-card">
                    <table class="table mb-0 text-nowrap table-centered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="user" class="icon-xs me-2"></i>Enviado por
                                    </div>
                                </th>
                                <th scope="col">
                                    <i data-feather="bookmark" class="icon-xs me-2"></i>Assunto
                                </th>
                                <th scope="col">
                                    <i data-feather="mail" class="icon-xs me-2"></i>Mensagem
                                </th>
                                <th scope="col" class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i data-feather="calendar" class="icon-xs me-2"></i>Data de Envio
                                    </div>
                                </th>
                                <th scope="col" class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i data-feather="settings" class="icon-xs me-2"></i>Ações
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($messages as $message)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <a href="{{ asset('storage/' . $message->user->file->path) }}"
                                                    data-fancybox>
                                                    <img class="avatar-md avatar rounded-circle"
                                                        src="{{ asset('storage/' . $message->user->file->path) }}"
                                                        alt="">
                                                </a>
                                            </div>
                                            <div class="ms-3 lh-1">
                                                <h5 class="mb-1">{{ $message->user->name }}</h5>
                                                <p class="mb-0">{{ $message->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $message->subject }}
                                    </td>
                                    <td>
                                        <div class="text-resume">
                                            {!! $message->description_resume !!}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        {{ $message->created_at_formatted }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('dashboard.lessons.messages.edit', [$lesson, $message]) }}"
                                            class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar"
                                            data-bs-toggle="tooltip">
                                            <i data-feather="edit" class="icon-xs"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                            onclick="confirmDelete('delete-item-{{ $message->id }}')" title="Apagar"
                                            data-bs-toggle="tooltip">
                                            <i data-feather="trash-2" class="icon-xs"></i>
                                        </button>
                                        <form class="d-none" id="delete-item-{{ $message->id }}" method="POST"
                                            action="{{ route('dashboard.lessons.messages.destroy', [$lesson, $message]) }}">
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
            @if ($messages->isNotEmpty() && $messages->hasPages())
                <div class="card-footer">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
