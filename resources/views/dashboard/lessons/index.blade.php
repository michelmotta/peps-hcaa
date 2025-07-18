@php
    use Illuminate\Support\Str;
    use App\Enums\LessonStatusEnum;
@endphp

@extends('templates.dashboard')

@section('content')
    {{-- Page Header --}}
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="layers" class="nav-icon me-2 icon-md"></i>
                Gerenciar Aulas
            </h1>
        </div>
    </div>

    <div class="container-fluid">
        {{-- Main Control Card --}}
        <div class="card shadow-sm mt-4 mb-8">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Aulas</h3>
                <a href="{{ route('dashboard.lessons.create') }}" class="btn btn-primary text-nowrap">
                    <i data-feather="plus" class="nav-icon icon-xs"></i>
                    <span class="ms-1">Nova Aula</span>
                </a>
            </div>
        </div>

        {{-- Accordion Filter Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4">Visão Geral</h4>
                <div class="row text-center mb-4">
                    <div class="col-6 col-md-3">
                        <h3 class="display-6 text-success mb-0">{{ $stats['published_count'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Publicadas</p>
                    </div>
                    <div class="col-6 col-md-3">
                        <h3 class="display-6 text-info mb-0">{{ $stats['awaiting_count'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Aguardando</p>
                    </div>
                    <div class="col-6 col-md-3 mt-3 mt-md-0">
                        <h3 class="display-6 text-warning mb-0">{{ $stats['draft_count'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Rascunhos</p>
                    </div>
                    <div class="col-6 col-md-3 mt-3 mt-md-0">
                        <h3 class="display-6 text-danger mb-0">{{ $stats['doubts_count'] ?? 0 }}</h3>
                        <p class="text-muted mb-0">Dúvidas Pendentes</p>
                    </div>
                </div>

                <hr>

                <div class="accordion" id="filterAccordion">
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed bg-transparent shadow-none" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="false"
                                aria-controls="collapseFilters">
                                <i data-feather="filter" class="me-2"></i> Filtros Avançados
                            </button>
                        </h2>
                        <div id="collapseFilters" class="accordion-collapse collapse" aria-labelledby="headingOne"
                            data-bs-parent="#filterAccordion">
                            <div class="accordion-body">
                                <form method="GET" action="{{ route('dashboard.lessons.index') }}">
                                    <div class="row align-items-end g-3">
                                        <div class="col-lg-4 col-md-12">
                                            <label class="form-label" for="q">Pesquisar</label>
                                            <input type="search" class="form-control" id="q" name="q"
                                                value="{{ request('q') }}" placeholder="Pesquisar aulas por nome...">
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <label class="form-label" for="status">Filtrar por Status</label>
                                            <select name="status" id="status" class="form-select">
                                                <option value="">Todos os Status</option>
                                                <option value="{{ LessonStatusEnum::RASCUNHO->value }}"
                                                    @selected(request('status') == LessonStatusEnum::RASCUNHO->value)>Rascunho</option>
                                                <option value="{{ LessonStatusEnum::AGUARDANDO_PUBLICACAO->value }}"
                                                    @selected(request('status') == LessonStatusEnum::AGUARDANDO_PUBLICACAO->value)>Aguardando</option>
                                                <option value="{{ LessonStatusEnum::PUBLICADA->value }}"
                                                    @selected(request('status') == LessonStatusEnum::PUBLICADA->value)>Publicada</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <label class="form-label" for="specialty">Filtrar por Especialidade</label>
                                            <select name="specialty" id="specialty" class="form-select">
                                                <option value="">Todas as Especialidades</option>
                                                @foreach ($specialties ?? [] as $specialty)
                                                    <option value="{{ $specialty->id }}" @selected(request('specialty') == $specialty->id)>
                                                        {{ $specialty->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-12">
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i data-feather="search" class="icon-xs me-1"></i>Filtrar
                                                </button>
                                                <a href="{{ route('dashboard.lessons.index') }}"
                                                    class="btn btn-outline-secondary" title="Limpar Filtros">
                                                    <i data-feather="rotate-ccw" class="icon-xs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row mt-8">
            @forelse ($lessons as $lesson)
                <div class="col-lg-6 mb-8">
                    <div class="card h-100">
                        <div class="row g-0 h-100">
                            <a href="{{ asset('storage/' . $lesson->file->path) }}"
                                data-fancybox="lesson-{{ $lesson->id }}" class="col-md-4 rounded-start"
                                style="background-image: url('{{ asset('storage/' . $lesson->file->path) }}'); background-size: cover; background-position: center; min-height: 250px;">
                            </a>

                            <div class="col-md-8">
                                <div class="card-body d-flex flex-column h-100 p-4">
                                    <div class="flex-grow-1">
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            @foreach ($lesson->specialties as $specialty)
                                                <a href="#"
                                                    class="badge bg-light text-dark text-decoration-none fw-normal">
                                                    <i data-feather="tag" class="icon-xs me-1"></i>
                                                    {{ $specialty->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                        <h3 class="mb-3 fw-bold">{{ $lesson->name }}</h3>
                                        <div class="d-flex justify-content-center text-center my-3">
                                            <a href="{{ route('dashboard.lessons.topics.index', $lesson->id) }}"
                                                class="text-inherit mx-3" title="Tópicos">
                                                <i data-feather="list" class="icon-sm"></i>
                                                <span
                                                    class="d-block fs-6 fw-bold mt-1">{{ $lesson->topics->count() }}</span>
                                                <small class="text-muted mb-0">Tópicos</small>
                                            </a>
                                            <a href="{{ route('dashboard.lessons.subscriptions.index', $lesson->id) }}"
                                                class="text-inherit mx-3" title="Inscrições">
                                                <i data-feather="users" class="icon-sm"></i>
                                                <span
                                                    class="d-block fs-6 fw-bold mt-1">{{ $lesson->subscriptions->count() }}</span>
                                                <small class="text-muted mb-0">Inscrições</small>
                                            </a>
                                            <a href="{{ route('dashboard.lessons.doubts.index', $lesson->id) }}"
                                                class="text-inherit mx-3" title="Dúvidas">
                                                <i data-feather="help-circle" class="icon-sm"></i>
                                                <span
                                                    class="d-block fs-6 fw-bold mt-1">{{ $lesson->doubts->count() }}</span>
                                                <small class="text-muted mb-0">Dúvidas</small>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- MODIFIED: Footer items have been reordered --}}
                                    <div class="mt-auto pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center">

                                            {{-- Item 1: Status Badge (now on the left) --}}
                                            <div>
                                                @if ($lesson->lesson_status === LessonStatusEnum::RASCUNHO->value)
                                                    <span class="badge bg-warning"><i data-feather="edit-3"
                                                            class="icon-xs me-1"></i>Rascunho</span>
                                                @elseif ($lesson->lesson_status === LessonStatusEnum::AGUARDANDO_PUBLICACAO->value)
                                                    <span class="badge bg-info"><i data-feather="clock"
                                                            class="icon-xs me-1"></i>Aguardando</span>
                                                @elseif ($lesson->lesson_status === LessonStatusEnum::PUBLICADA->value)
                                                    <span class="badge bg-success"><i data-feather="check-circle"
                                                            class="icon-xs me-1"></i>Publicada</span>
                                                @endif
                                            </div>

                                            {{-- Item 2: Professor Info (now in the center) --}}
                                            <div>
                                                @can('isCoordenador')
                                                    <div class="d-flex align-items-center">
                                                        @if ($lesson->teacher && $lesson->teacher->file)
                                                            <a href="{{ asset('storage/' . $lesson->teacher->file->path) }}"
                                                                data-fancybox class="me-2">
                                                                <img src="{{ asset('storage/' . $lesson->teacher->file->path) }}"
                                                                    class="rounded-circle" alt="Avatar" width="30"
                                                                    height="30" style="object-fit: cover;">
                                                            </a>
                                                        @else
                                                            <img src="https://placehold.co/30x30/EBF4FF/7F9CF5?text={{ strtoupper(substr($lesson->teacher->name, 0, 1)) }}"
                                                                alt="Avatar" class="rounded-circle me-2">
                                                        @endif
                                                        <div>
                                                            <p class="mb-0 small fw-bold">{{ $lesson->teacher->name }}</p>
                                                            <small
                                                                class="text-muted">{{ $lesson->teacher->expertise }}</small>
                                                        </div>
                                                    </div>
                                                @endcan
                                            </div>

                                            {{-- Item 3: Action Buttons (now on the right) --}}
                                            <div class="d-flex align-items-center gap-1">
                                                @can('canProfessorAskForPublication', $lesson)
                                                    <button
                                                        onclick="event.preventDefault(); document.getElementById('ask-publish-{{ $lesson->id }}').submit();"
                                                        class="btn btn-ghost btn-icon btn-sm rounded-circle text-primary"
                                                        title="Solicitar Publicação" data-bs-toggle="tooltip"><i
                                                            data-feather="send" class="icon-xs"></i></button>
                                                @endcan
                                                @can('canCoordenadorPublish', $lesson)
                                                    <button
                                                        onclick="event.preventDefault(); document.getElementById('publish-{{ $lesson->id }}').submit();"
                                                        class="btn btn-ghost btn-icon btn-sm rounded-circle text-success"
                                                        title="Publicar" data-bs-toggle="tooltip"><i
                                                            data-feather="check-circle" class="icon-xs"></i></button>
                                                @endcan
                                                @can('canCoordenadorUnpublish', $lesson)
                                                    <button
                                                        onclick="event.preventDefault(); document.getElementById('unpublish-{{ $lesson->id }}').submit();"
                                                        class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                                        title="Despublicar" data-bs-toggle="tooltip"><i
                                                            data-feather="x-circle" class="icon-xs"></i></button>
                                                @endcan
                                                <a href="{{ route('dashboard.lessons.edit', $lesson->id) }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar"
                                                    data-bs-toggle="tooltip"><i data-feather="edit"
                                                        class="icon-xs"></i></a>
                                                <button onclick="confirmDelete('delete-item-{{ $lesson->id }}')"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                                    title="Apagar" data-bs-toggle="tooltip"><i data-feather="trash-2"
                                                        class="icon-xs"></i></button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hidden Forms for Actions --}}
                @can('canProfessorAskForPublication', $lesson)
                    <form id="ask-publish-{{ $lesson->id }}" class="d-none" method="POST"
                        action="{{ route('dashboard.lessons.change-status', $lesson->id) }}">@csrf<input type="hidden"
                            name="status_id" value="2"></form>
                @endcan
                @can('canCoordenadorPublish', $lesson)
                    <form id="publish-{{ $lesson->id }}" class="d-none" method="POST"
                        action="{{ route('dashboard.lessons.change-status', $lesson->id) }}">@csrf<input type="hidden"
                            name="status_id" value="3"></form>
                @endcan
                @can('canCoordenadorUnpublish', $lesson)
                    <form id="unpublish-{{ $lesson->id }}" class="d-none" method="POST"
                        action="{{ route('dashboard.lessons.change-status', $lesson->id) }}">@csrf<input type="hidden"
                            name="status_id" value="2"></form>
                @endcan
                <form id="delete-item-{{ $lesson->id }}" class="d-none" method="POST"
                    action="{{ route('dashboard.lessons.destroy', $lesson->id) }}">@csrf @method('DELETE')</form>
            @empty
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center py-5">
                            <i data-feather="inbox" class="icon-lg text-muted mb-3"></i>
                            <h4 class="text-muted">Nenhuma aula encontrada</h4>
                            <p class="text-muted mb-0">Tente ajustar os filtros ou crie sua primeira aula.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        @if ($lessons->isNotEmpty() && $lessons->hasPages())
            <div class="card card-pagination shadow-sm mt-4">
                <div class="card-body">
                    {{ $lessons->withQueryString()->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
