@php
    use Illuminate\Support\Str;
    use App\Enums\LessonStatusEnum;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="users" class="nav-icon me-2 icon-md"></i>
                Inscrições: {{ $lesson->name }}
            </h1>
        </div>
    </div>
    <div class="container-fluid lessons-grid">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('dashboard.lessons.index') }}" class="btn btn-primary btn-md mb-5">
                    <i data-feather="arrow-left" class="nav-icon me-2 icon-xs"></i>
                    Voltar
                </a>
                <div class="d-grid d-lg-block ms-auto text-end mb-5">
                    <a href="{{ route('dashboard.lessons.subscriptions.create', $lesson) }}" class="btn btn-primary btn-lg">
                        <i data-feather="plus" class="nav-icon me-2 icon-xs"></i>
                        Nova Inscrição
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Estudante Inscritos</h3>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('dashboard.lessons.subscriptions.index', $lesson) }}">
                                    <input type="search" class="form-control w-100" name="q" style="height: 50px"
                                        value="{{ request('q') }}" placeholder="Pesquisar estudantes...">
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
                                        <th scope="col" class="text-center">Data de Início</th>
                                        <th scope="col" class="text-center">Conclusão</th>
                                        <th scope="col" class="text-center">Data de Conclusão</th>
                                        <th scope="col" class="text-center">Nota</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subscriptions as $subscription)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <a href="{{ asset('storage/' . $subscription->user->file->path) }}"
                                                            data-fancybox>
                                                            <img class="avatar-md avatar rounded-circle"
                                                                src="{{ asset('storage/' . $subscription->user->file->path) }}"
                                                                alt="">
                                                        </a>
                                                    </div>
                                                    <div class="ms-3 lh-1">
                                                        <h5 class="mb-1">{{ $subscription->user->name }}</h5>
                                                        <p class="mb-0">{{ $subscription->user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                {{ $subscription->created_at_formatted }}
                                            </td>
                                            <td class="text-center">
                                                @if ($subscription->finished)
                                                    <span class="badge bg-success">
                                                        <i data-feather="check-circle" class="icon-xs me-1"></i>
                                                        Concluído
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i data-feather="clock" class="icon-xs me-1"></i>
                                                        Em Andamento
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $subscription->finished_at_formatted }}
                                            </td>
                                            <td class="text-center">
                                                {{ $subscription->score }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.lessons.subscriptions.edit', [$lesson, $subscription]) }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle"
                                                    data-template="editTwo" title="Editar" data-bs-toggle="tooltip">
                                                    <i data-feather="edit" class="icon-xs"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                                    data-template="trashOne"
                                                    onclick="confirmDelete('delete-item-{{ $subscription->id }}')"
                                                    title="Apagar" data-bs-toggle="tooltip">
                                                    <i data-feather="trash-2" class="icon-xs"></i>
                                                </button>
                                                <form class="d-none" id="delete-item-{{ $subscription->id }}" method="POST"
                                                    action="{{ route('dashboard.lessons.subscriptions.destroy', [$lesson, $subscription]) }}">
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
                        {{ $subscriptions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
