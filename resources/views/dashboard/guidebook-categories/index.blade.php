@php
    use Illuminate\Support\Str;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="bookmark" class="nav-icon me-2 icon-md"></i>
                Gerenciar Categorias de Manuais
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 mb-5">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Categorias de Manuais</h3>
                        <div class="d-flex align-items-center">
                            <form method="GET" action="{{ route('dashboard.guidebook-categories.index') }}"
                                class="me-2">
                                <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                                    placeholder="Pesquisar categorias...">
                            </form>
                            <a href="{{ route('dashboard.guidebook-categories.create') }}"
                                class="btn btn-primary text-nowrap">
                                <i data-feather="plus" class="nav-icon icon-xs"></i>
                                <span class="ms-1">Nova Categoria</span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 text-nowrap table-centered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Nome da Categoria</th>
                                        <th scope="col" class="text-center">Manuais</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="icon-shape icon-md bg-light-primary text-primary rounded-2 me-3">
                                                        <i data-feather="{{ $category->icon ?? 'book-open' }}"></i>
                                                    </div>
                                                    <h5 class="mb-0">{{ $category->name }}</h5>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $category->guidebooks_count }}</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.guidebook-categories.edit', $category->id) }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar"
                                                    data-bs-toggle="tooltip">
                                                    <i data-feather="edit" class="icon-xs"></i>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                                    onclick="confirmDelete('delete-item-{{ $category->id }}')"
                                                    title="Apagar" data-bs-toggle="tooltip">
                                                    <i data-feather="trash-2" class="icon-xs"></i>
                                                </button>
                                                <form class="d-none" id="delete-item-{{ $category->id }}" method="POST"
                                                    action="{{ route('dashboard.guidebook-categories.destroy', $category->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                Nenhuma categoria encontrada.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if ($categories->isNotEmpty() && $categories->hasPages())
                        <div class="card-footer">
                            {{ $categories->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
