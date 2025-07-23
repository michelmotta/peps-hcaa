@php
    use Illuminate\Support\Str;
@endphp

@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="file-text" class="nav-icon me-2 icon-md"></i>
                Gerenciar Biblioteca
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="card shadow-sm mb-4 mt-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Arquivos</h3>
                <div class="d-flex align-items-center">
                    <form method="GET" action="{{ route('dashboard.libraries.index') }}" class="me-2">
                        <div class="input-group">
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                                placeholder="Pesquisar arquivos..">
                            <button class="btn btn-primary" type="submit" title="Buscar">
                                <i data-feather="search" class="icon-xs"></i>
                            </button>
                        </div>
                    </form>
                    <a href="{{ route('dashboard.libraries.create') }}" class="btn btn-primary text-nowrap">
                        <i data-feather="plus" class="nav-icon icon-xs"></i>
                        <span class="ms-1">Novo Arquivo</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="row g-4">
            @forelse ($libraries as $library)
                <div class="col-4">
                    <div class="card shadow-sm library-card">
                        <div class="row g-0">
                            <div class="col-md-4">
                                @if ($library->file->thumbnail_path)
                                    <img class="w-100 h-100 rounded-start"
                                        src="{{ asset('storage/' . $library->file->thumbnail_path) }}"
                                        alt="Preview de {{ $library->title }}"
                                        style="object-fit: cover; min-height: 180px;">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light rounded-start"
                                        style="min-height: 180px;">
                                        <i data-feather="file-text" class="icon-xl text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column h-100">
                                    <div class="flex-grow-1">
                                        <h4 class="card-title text-truncate mb-2">{{ $library->title }}</h4>
                                        <div class="small text-muted mb-3">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="user" class="icon-xs me-2"></i>
                                                <span>Postado por: {{ $library->user->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <div class="small text-muted d-flex">
                                            <span class="d-flex align-items-center me-3" title="Tamanho do Arquivo"
                                                data-bs-toggle="tooltip">
                                                <i data-feather="hard-drive" class="icon-xs me-1"></i>
                                                {{ $library->file->size_in_mb }}
                                            </span>
                                            <span class="d-flex align-items-center" title="Data de Criação"
                                                data-bs-toggle="tooltip">
                                                <i data-feather="calendar" class="icon-xs me-1"></i>
                                                {{ $library->created_at->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        <div>
                                            <a href="{{ asset('storage/' . $library->file->path) }}" download
                                                class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Baixar"
                                                data-bs-toggle="tooltip">
                                                <i data-feather="download" class="icon-xs"></i>
                                            </a>
                                            <a href="{{ route('dashboard.libraries.edit', $library->id) }}"
                                                class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar"
                                                data-bs-toggle="tooltip">
                                                <i data-feather="edit" class="icon-xs"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                                onclick="confirmDelete('delete-item-{{ $library->id }}')" title="Apagar"
                                                data-bs-toggle="tooltip">
                                                <i data-feather="trash-2" class="icon-xs"></i>
                                            </button>
                                            <form class="d-none" id="delete-item-{{ $library->id }}" method="POST"
                                                action="{{ route('dashboard.libraries.destroy', $library->id) }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 bg-white">
                        <div class="card-body text-center py-5">
                            <i data-feather="alert-circle" class="icon-xl text-muted"></i>
                            <h4 class="text-muted mt-2">Nenhum arquivo encontrado.</h4>
                            <p>Clique em "Novo Arquivo" para começar.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        @if ($libraries->isNotEmpty() && $libraries->hasPages())
            <div class="card card-pagination shadow-sm mt-4">
                <div class="card-body">
                    {{ $libraries->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
