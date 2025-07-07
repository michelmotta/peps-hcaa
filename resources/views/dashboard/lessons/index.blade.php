@php
    use Illuminate\Support\Str;
    use App\Enums\LessonStatusEnum;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="layers" class="nav-icon me-2 icon-md"></i>
                Gerenciar Aulas
            </h1>
        </div>
    </div>
    <div class="container-fluid lessons-grid">
        <div class="row">
            <div class="col-md-12">
                <div class="d-grid d-lg-block ms-auto text-end mb-5">
                    <a href="{{ route('dashboard.lessons.create') }}" class="btn btn-primary btn-lg">
                        <i data-feather="plus" class="nav-icon me-2 icon-xs"></i>
                        Nova Aula
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Aulas</h3>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('dashboard.lessons.index') }}">
                                    <input type="search" class="form-control w-100" name="q" style="height: 50px"
                                        value="{{ request('q') }}" placeholder="Pesquisar aulas...">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table mb-0 text-nowrap table-centered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Nome</th>
                                        <th scope="col" class="text-center">Tópicos</th>
                                        <th scope="col" class="text-center">Inscrições</th>
                                        <th scope="col" class="text-center">Dúvidas</th>
                                        @can('isProfessor')
                                            <th scope="col" class="text-center">Feedbacks</th>
                                        @endcan
                                        @can('isCoordenador')
                                            <th scope="col" class="text-center">Professor(a)</th>
                                        @endcan
                                        <th scope="col" class="text-center">Status</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($lessons as $lesson)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <a href="{{ asset('storage/' . $lesson->file->path) }}"
                                                            data-fancybox>
                                                            <img class="avatar-md avatar rounded-circle"
                                                                src="{{ asset('storage/' . $lesson->file->path) }}"
                                                                alt="">
                                                        </a>
                                                    </div>
                                                    <div class="ms-3 lh-1">
                                                        <h5 class="mb-1">{{ $lesson->name }}</h5>
                                                        <p class="mb-0">{{ $lesson->specialty->name }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.lessons.topics.index', $lesson->id) }}"
                                                    class="">
                                                    <span class="item-content">
                                                        <i class="icon-xs" data-feather="list"></i>
                                                        ({{ $lesson->topics->count() }})
                                                    </span>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.lessons.subscriptions.index', $lesson->id) }}">
                                                    <span class="item-content">
                                                        <i class="icon-xs" data-feather="users"></i>
                                                        ({{ $lesson->subscriptions->count() }})
                                                    </span>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.lessons.doubts.index', $lesson->id) }}">
                                                    <span class="item-content">
                                                        <i class="icon-xs" data-feather="help-circle"></i>
                                                        ({{ $lesson->doubts->count() }})
                                                    </span>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <a href="#">
                                                    <span class="item-content">
                                                        <i class="icon-xs" data-feather="message-circle"></i>
                                                        ({{ $lesson->feedbacks->count() }})
                                                    </span>
                                                </a>
                                            </td>
                                            @can('isCoordenador')
                                                <td class="text-center">
                                                    <span class="badge badge-primary-soft">{{ $lesson->teacher->name }}</span>
                                                </td>
                                            @endcan
                                            <td class="text-center">
                                                @if ($lesson->lesson_status === LessonStatusEnum::RASCUNHO->value)
                                                    <span class="badge bg-warning">
                                                        <i data-feather="info" class="icon-xs me-1"></i>
                                                        {{ LessonStatusEnum::getLessonStatusNameById($lesson->lesson_status) }}
                                                    </span>
                                                @endif
                                                @if ($lesson->lesson_status === LessonStatusEnum::AGUARDANDO_PUBLICACAO->value)
                                                    <span class="badge bg-info">
                                                        <i data-feather="clock" class="icon-xs me-1"></i>
                                                        {{ LessonStatusEnum::getLessonStatusNameById($lesson->lesson_status) }}
                                                    </span>
                                                @endif
                                                @if ($lesson->lesson_status === LessonStatusEnum::PUBLICADA->value)
                                                    <span class="badge bg-success">
                                                        <i data-feather="check-circle" class="icon-xs me-1"></i>
                                                        {{ LessonStatusEnum::getLessonStatusNameById($lesson->lesson_status) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @can('canProfessorAskForPublication', $lesson)
                                                    <form method="POST"
                                                        action="{{ route('dashboard.lessons.change-status', $lesson->id) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status_id" value="2">
                                                        <button
                                                            class="btn btn-ghost btn-icon btn-sm rounded-circle text-primary"
                                                            type="submit" title="Solicitar publicação"
                                                            data-bs-toggle="tooltip">
                                                            <i data-feather="send" class="icon-xs"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('canCoordenadorPublish', $lesson)
                                                    <form method="POST"
                                                        action="{{ route('dashboard.lessons.change-status', $lesson->id) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status_id" value="3">
                                                        <button
                                                            class="btn btn-ghost btn-icon btn-sm rounded-circle text-success"
                                                            type="submit" title="Publicar" data-bs-toggle="tooltip">
                                                            <i data-feather="check-circle" class="icon-xs"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('canCoordenadorUnpublish', $lesson)
                                                    <form method="POST"
                                                        action="{{ route('dashboard.lessons.change-status', $lesson->id) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status_id" value="2">
                                                        <button
                                                            class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                                            type="submit" title="Despublicar" data-bs-toggle="tooltip">
                                                            <i data-feather="x-circle" class="icon-xs"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                                <a href="{{ route('dashboard.lessons.edit', $lesson->id) }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle"
                                                    data-template="editTwo" title="Editar" data-bs-toggle="tooltip">
                                                    <i data-feather="edit" class="icon-xs"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                                    data-template="trashOne"
                                                    onclick="confirmDelete('delete-item-{{ $lesson->id }}')"
                                                    title="Apagar" data-bs-toggle="tooltip">
                                                    <i data-feather="trash-2" class="icon-xs"></i>
                                                </button>
                                                <form class="d-none" id="delete-item-{{ $lesson->id }}" method="POST"
                                                    action="{{ route('dashboard.lessons.destroy', $lesson->id) }}">
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
                        {{ $lessons->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
