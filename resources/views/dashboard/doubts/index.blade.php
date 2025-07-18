@php
    use Illuminate\Support\Str;
@endphp
@extends('templates.dashboard')
@section('content')
    {{-- Page Header --}}
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="message-circle" class="nav-icon me-2 icon-md"></i>
                Dúvidas: {{ $lesson->name }}
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
                    <h3 class="mb-0">Dúvidas</h3>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="{{ route('dashboard.lessons.doubts.index', $lesson) }}" class="mb-0">
                        <div class="input-group">
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                                placeholder="Pesquisar dúvidas...">
                            <button class="btn btn-primary" type="submit" title="Buscar"><i data-feather="search"
                                    class="icon-xs"></i></button>
                        </div>
                    </form>
                    <a href="{{ route('dashboard.lessons.doubts.create', $lesson) }}" class="btn btn-primary text-nowrap">
                        <i data-feather="plus" class="nav-icon icon-xs"></i>
                        <span class="ms-1">Nova Dúvida</span>
                    </a>
                </div>
            </div>
        </div>
        @forelse ($doubts as $doubt)
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <div class="d-flex">
                        <img src="{{ asset('storage/' . $doubt->user->file->path) }}" class="avatar rounded-circle me-3"
                            alt="Avatar">
                        <div class="flex-grow-1">
                            <div class="chat-bubble p-3 rounded-3">
                                <p class="mb-0">{{ $doubt->doubt }}</p>
                            </div>
                            <small class="text-muted mt-1 d-block">{{ $doubt->user->name }} &middot;
                                {{ $doubt->created_at->diffForHumans() }}</small>
                        </div>
                        <div class="d-flex gap-1 ms-3">
                            <a href="{{ route('dashboard.lessons.doubts.edit', [$lesson, $doubt]) }}"
                                class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar/Responder">
                                <i data-feather="edit" class="icon-xs"></i>
                            </a>
                            <button type="button" class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                onclick="confirmDelete('delete-item-{{ $doubt->id }}')" title="Apagar">
                                <i data-feather="trash-2" class="icon-xs"></i>
                            </button>
                            <form class="d-none" id="delete-item-{{ $doubt->id }}" method="POST"
                                action="{{ route('dashboard.lessons.doubts.destroy', [$lesson, $doubt]) }}">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>

                    @if ($doubt->answered)
                        <hr class="my-4">
                        <div class="d-flex justify-content-end">
                            <div class="flex-grow-1 text-end">
                                <div class="chat-bubble-answer d-inline-block text-start p-3 rounded-3">
                                    <p class="mb-0">{{ $doubt->description }}</p>
                                </div>
                                <small class="text-muted mt-1 d-block">Respondido em
                                    {{ $doubt->answered_at_formatted }}</small>
                            </div>
                            <img src="{{ asset('storage/' . Auth::user()->file->path) }}"
                                class="avatar rounded-circle ms-3" alt="">
                        </div>
                    @else
                        <div class="text-center p-3 mt-3 bg-light rounded">
                            <p class="mb-2 text-muted">Esta dúvida ainda não foi respondida.</p>
                            <a href="{{ route('dashboard.lessons.doubts.edit', [$lesson, $doubt]) }}"
                                class="btn btn-success">
                                <i data-feather="corner-up-left" class="icon-xs me-1"></i> Responder
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i data-feather="inbox" class="icon-xl text-muted"></i>
                    <h4 class="text-muted mt-3">Nenhuma dúvida por aqui</h4>
                    <p class="text-muted mb-0">Seja o primeiro a enviar uma dúvida sobre esta aula.</p>
                </div>
            </div>
        @endforelse

        @if ($doubts->hasPages())
            <div class="card-footer bg-transparent">
                {{ $doubts->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
<style>
    .chat-bubble {
        background-color: #f1f3f5;
        max-width: 85%;
        display: inline-block;
    }

    .chat-bubble-answer {
        background-color: #e6f7ff;
        max-width: 85%;
    }
</style>
