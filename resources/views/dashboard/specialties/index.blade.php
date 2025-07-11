@extends('templates.dashboard')

@section('content')
    {{-- Page Header --}}
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="bookmark" class="nav-icon me-2 icon-md"></i>
                Gerenciar Especialidades
            </h1>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">

            {{-- LEFT COLUMN (Master List) --}}
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Especialidades</h4>
                        {{-- CHANGE 1: Added text to the button --}}
                        <a href="{{ route('dashboard.specialties.create') }}" class="btn btn-primary btn-sm text-nowrap"
                            title="Nova Especialidade">
                            <i data-feather="plus" class="icon-xs me-1"></i>
                            Nova Especialidade
                        </a>
                    </div>

                    <div class="card-header bg-white border-top">
                        <form method="GET" action="{{ route('dashboard.specialties.index') }}">
                            <div class="input-group">
                                <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                                    placeholder="Pesquisar...">
                                {{-- CHANGE 2: Changed button color from btn-light to btn-primary --}}
                                <button class="btn btn-primary" type="submit" title="Buscar">
                                    <i data-feather="search" class="icon-xs"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="list-group list-group-flush">
                        @forelse ($specialties as $specialty)
                            <a href="{{ route('dashboard.specialties.index', array_merge(request()->query(), ['selected' => $specialty->id])) }}"
                                class="list-group-item list-group-item-action d-flex align-items-center {{ isset($selectedSpecialty) && $selectedSpecialty->id == $specialty->id ? 'active' : '' }}">

                                @if ($specialty->file)
                                    <img class="avatar avatar-sm me-3"
                                        src="{{ asset('storage/' . $specialty->file->path) }}" alt="Logo">
                                @else
                                    <div class="avatar avatar-sm me-3 bg-light rounded-circle"></div>
                                @endif
                                <span class="flex-grow-1">{{ $specialty->name }}</span>
                                <i data-feather="chevron-right" class="icon-xs"></i>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted">
                                <p>Nenhuma especialidade encontrada.</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($specialties->hasPages())
                        <div class="card-footer">
                            {{ $specialties->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT COLUMN (Detail View) --}}
            <div class="col-lg-8">
                <div class="card h-100 shadow-sm">
                    @if (isset($selectedSpecialty))
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Detalhes</h4>
                            <div>
                                <a href="{{ route('dashboard.specialties.edit', $selectedSpecialty->id) }}"
                                    class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar"
                                    data-bs-toggle="tooltip">
                                    <i data-feather="edit" class="icon-xs"></i>
                                </a>
                                <button type="button" class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                    onclick="confirmDelete('delete-item-{{ $selectedSpecialty->id }}')" title="Apagar"
                                    data-bs-toggle="tooltip">
                                    <i data-feather="trash-2" class="icon-xs"></i>
                                </button>
                                <form class="d-none" id="delete-item-{{ $selectedSpecialty->id }}" method="POST"
                                    action="{{ route('dashboard.specialties.destroy', $selectedSpecialty->id) }}">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="d-flex align-items-center mb-4">
                                @if ($selectedSpecialty->file)
                                    <img class="avatar avatar-xl me-4"
                                        src="{{ asset('storage/' . $selectedSpecialty->file->path) }}"
                                        alt="Logo de {{ $selectedSpecialty->name }}">
                                @endif
                                <div>
                                    <h2 class="mb-0">{{ $selectedSpecialty->name }}</h2>
                                    <span class="text-muted">ID: {{ $selectedSpecialty->id }}</span>
                                </div>
                            </div>

                            <hr>

                            <h5>Subespecialidades</h5>
                            @forelse ($selectedSpecialty->children as $sub)
                                <span class="badge badge-secondary-soft fs-5 me-1">{{ $sub->name }}</span>
                            @empty
                                <div class="text-muted">
                                    <i data-feather="info" class="icon-xs me-1"></i>
                                    Nenhuma subespecialidade cadastrada.
                                </div>
                            @endforelse
                        </div>
                    @else
                        <div class="card-body d-flex h-100 justify-content-center align-items-center">
                            <div class="text-center text-muted">
                                <i data-feather="arrow-left" class="icon-lg mb-2"></i>
                                <h4>Selecione uma especialidade</h4>
                                <p>Escolha um item da lista à esquerda para ver seus detalhes.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
