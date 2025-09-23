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
    <div class="container-fluid">
        <div class="card shadow-sm border-0 mb-4 mt-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('dashboard.lessons.index') }}"
                        class="btn btn-outline-primary d-flex align-items-center">
                        <i data-feather="arrow-left" class="nav-icon me-2 icon-xs"></i>
                        Voltar
                    </a>
                    <h3 class="mb-0">Inscrições</h3>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="{{ route('dashboard.lessons.subscriptions.index', $lesson) }}"
                        class="mb-0">
                        <div class="input-group">
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                                placeholder="Pesquisar estudantes...">
                            <button class="btn btn-primary" type="submit" title="Buscar"><i data-feather="search"
                                    class="icon-xs"></i></button>
                        </div>
                    </form>
                    <a href="{{ route('dashboard.lessons.subscriptions.create', $lesson) }}"
                        class="btn btn-primary text-nowrap">
                        <i data-feather="plus" class="nav-icon icon-xs"></i>
                        <span class="ms-1">Nova Inscrição</span>
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
                                        <i data-feather="user" class="icon-xs me-2"></i>Estudante
                                    </div>
                                </th>
                                <th scope="col" class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i data-feather="calendar" class="icon-xs me-2"></i>Data de Início
                                    </div>
                                </th>
                                <th scope="col" class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i data-feather="activity" class="icon-xs me-2"></i>Conclusão
                                    </div>
                                </th>
                                <th scope="col" class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i data-feather="flag" class="icon-xs me-2"></i>Data de Conclusão
                                    </div>
                                </th>
                                <th scope="col" class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <i data-feather="award" class="icon-xs me-2"></i>Nota
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
                            @foreach ($subscriptions as $subscription)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <a href="{{ $subscription->user->getAvatarUrl(150) }}" data-fancybox>
                                                    <img class="avatar-md avatar rounded-circle"
                                                        src="{{ $subscription->user->getAvatarUrl(150) }}"
                                                        alt="Foto de {{ $subscription->user->name }}">
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
                                            class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar"
                                            data-bs-toggle="tooltip">
                                            <i data-feather="edit" class="icon-xs"></i>
                                        </a>

                                        <button type="button"
                                            class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                            onclick="confirmDelete('delete-item-{{ $subscription->id }}')" title="Apagar"
                                            data-bs-toggle="tooltip">
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
            @if ($subscriptions->isNotEmpty() && $subscriptions->hasPages())
                <div class="card-footer">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
