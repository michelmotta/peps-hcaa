@php
    use Illuminate\Support\Str;
@endphp
@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="mb-5 p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="list" class="nav-icon me-2 icon-md"></i>
                Tópicos: {{ $lesson->name }}
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('dashboard.lessons.index') }}" class="btn btn-primary btn-md mb-5">
                    <i data-feather="arrow-left" class="nav-icon me-2 icon-xs"></i>
                    Voltar
                </a>
                <div class="d-grid d-lg-block ms-auto text-end mb-5">
                    <a href="{{ route('dashboard.lessons.topics.create', $lesson) }}" class="btn btn-primary btn-lg">
                        <i data-feather="plus" class="nav-icon me-2 icon-xs"></i>
                        Novo Topico
                    </a>
                </div>
            </div>
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-9 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Lista de tópicos: {{ $lesson->name }}</h3>
                            </div>
                            <div class="col-md-3">
                                <form method="GET" action="{{ route('dashboard.lessons.topics.index', $lesson) }}">
                                    <input type="search" class="form-control w-100" name="q" style="height: 50px"
                                        value="{{ request('q') }}" placeholder="Pesquisar tópicos...">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table mb-0 text-nowrap table-centered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Tópico</th>
                                        <th scope="col">Descrição</th>
                                        <th scope="col" class="text-center">Video</th>
                                        <th scope="col" class="text-center">Anexos</th>
                                        <th scope="col" class="text-center">Questões</th>
                                        <th scope="col" class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topics as $topic)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <a href="{{ asset('storage/' . $topic->video->path) }}"
                                                            data-fancybox>
                                                            <img class="avatar-md avatar rounded-circle"
                                                                src="{{ asset('storage/' . $topic->video->thumbnail_path) }}"
                                                                alt="">
                                                        </a>
                                                    </div>
                                                    <div class="ms-3 lh-1">
                                                        <h5 class="mb-1">{{ $topic->title }}</h5>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{!! Str::limit(strip_tags($topic->description), 60) !!}</td>
                                            <td class="text-center">
                                                <a href="{{ asset('storage/' . $topic->video->path) }}" data-fancybox>
                                                    <i data-feather="play-circle" class="icon-sm"></i>
                                                    <span
                                                        class="badge badge-primary-soft">{{ $topic->video->duration }}</span>
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge badge-secondary-soft">{{ collect($topic->attachments)->count() }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge badge-secondary-soft">{{ collect($topic->quizzes)->count() }}</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('dashboard.lessons.topics.edit', [$lesson->id, $topic->id]) }}"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip"
                                                    data-template="editTwo" title="Editar" data-bs-toggle="tooltip">
                                                    <i data-feather="edit" class="icon-xs"></i>
                                                    <div id="editTwo" class="d-none">
                                                        <span>Edit</span>
                                                    </div>
                                                </a>
                                                <button type="button"
                                                    class="btn btn-ghost btn-icon btn-sm rounded-circle texttooltip text-danger"
                                                    data-template="trashOne"
                                                    onclick="confirmDelete('delete-item-{{ $topic->id }}')"
                                                    title="Apagar" data-bs-toggle="tooltip">
                                                    <i data-feather="trash-2" class="icon-xs"></i>
                                                    <div id="trashOne" class="d-none">
                                                        <span>Delete</span>
                                                    </div>
                                                </button>
                                                <form class="d-none" id="delete-item-{{ $topic->id }}" method="POST"
                                                    action="{{ route('dashboard.lessons.topics.destroy', [$lesson->id, $topic->id]) }}">
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
                        {{ $topics->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
