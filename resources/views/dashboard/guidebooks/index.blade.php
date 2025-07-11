@php
    use Illuminate\Support\Str;
    use App\Enums\GuidebookEnum;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="book" class="nav-icon me-2 icon-md"></i>
                Manuais do Sistema
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        {{-- Header with Centered Search and Create Button --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('dashboard.guidebooks.index') }}">
                            @if (request('category_id'))
                                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                            @endif
                            <div class="input-group">
                                <input type="search" class="form-control form-control-lg" name="q"
                                    value="{{ request('q') }}" placeholder="Pesquisar manuais...">
                                <button type="submit" class="btn btn-primary px-4"><i data-feather="search"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-3 text-end">
                        <a href="{{ route('dashboard.guidebooks.create') }}" class="btn btn-primary text-nowrap">
                            <i data-feather="plus" class="nav-icon icon-xs"></i>
                            <span class="ms-1">Novo Manual</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Left Sidebar for Category Navigation --}}
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Categorias</h4>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('dashboard.guidebooks.index') }}"
                            class="list-group-item list-group-item-action {{ !request('category_id') ? 'active' : '' }}">
                            Todas as Categorias
                        </a>
                        @foreach ($categories as $category)
                            <a href="{{ route('dashboard.guidebooks.index', ['category_id' => $category->id]) }}"
                                class="list-group-item list-group-item-action {{ request('category_id') == $category->id ? 'active' : '' }}">
                                <i data-feather="{{ $category->icon ?? 'book-open' }}" class="icon-xs me-2"></i>
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Panel with Accordion List --}}
            <div class="col-lg-9">
                <div class="accordion" id="guidebooksAccordion">
                    @forelse ($guidebooks as $guidebook)
                        <div class="accordion-item" id="guidebook-{{ $guidebook->id }}">
                            <h2 class="accordion-header" id="heading-{{ $guidebook->id }}">
                                <button class="accordion-button collapsed bg-white" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse-{{ $guidebook->id }}" aria-expanded="false"
                                    aria-controls="collapse-{{ $guidebook->id }}">
                                    {{-- ✅ Flexbox container for alignment --}}
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <span class="fw-bold">{{ $guidebook->title }}</span>

                                        {{-- ✅ Access Type Badge --}}
                                        @if ($guidebook->type === GuidebookEnum::INTERN)
                                            <span class="badge bg-secondary-soft text-secondary me-3">
                                                <i data-feather="lock"
                                                    class="icon-xs me-1"></i>{{ $guidebook->type->label() }}
                                            </span>
                                        @else
                                            <span class="badge bg-primary-soft text-primary me-3">
                                                <i data-feather="globe"
                                                    class="icon-xs me-1"></i>{{ $guidebook->type->label() }}
                                            </span>
                                        @endif
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse-{{ $guidebook->id }}" class="accordion-collapse collapse"
                                aria-labelledby="heading-{{ $guidebook->id }}" data-bs-parent="#guidebooksAccordion">
                                <div class="accordion-body guidebook-content bg-white">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h3 class="mb-0 d-flex align-items-center">
                                                <i data-feather="file-text" class="icon-sm me-2 text-muted"></i>
                                                {{ $guidebook->title }}
                                            </h3>
                                            <div class="small text-muted mt-2">
                                                <span>Categoria: {{ $guidebook->category?->name ?? 'N/A' }}</span>
                                                <span class="mx-1">|</span>
                                                <span>Última atualização:
                                                    {{ $guidebook->updated_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        </div>
                                        <div class="actions">
                                            <a href="{{ route('dashboard.guidebooks.edit', $guidebook->id) }}"
                                                class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar"
                                                data-bs-toggle="tooltip">
                                                <i data-feather="edit" class="icon-xs"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                                onclick="confirmDelete('delete-item-{{ $guidebook->id }}')" title="Apagar"
                                                data-bs-toggle="tooltip">
                                                <i data-feather="trash-2" class="icon-xs"></i>
                                            </button>
                                            <form class="d-none" id="delete-item-{{ $guidebook->id }}" method="POST"
                                                action="{{ route('dashboard.guidebooks.destroy', $guidebook->id) }}">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                    <hr class="mt-0">
                                    {!! $guidebook->description !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i data-feather="alert-circle" class="icon-xl text-muted"></i>
                                <h4 class="text-muted mt-2">Nenhum manual encontrado.</h4>
                                <p class="text-muted">Tente selecionar outra categoria ou limpar a busca.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
                @if ($guidebooks->isNotEmpty())
                    <div class="card card-pagination shadow-sm mt-4">
                        <div class="card-body">
                            {{ $guidebooks->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
