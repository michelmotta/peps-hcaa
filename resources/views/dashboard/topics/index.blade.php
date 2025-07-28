@php
    use Illuminate\Support\Str;
    use App\Enums\LessonStatusEnum;
@endphp
@extends('templates.dashboard')

@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center ">
                <i data-feather="list" class="nav-icon me-2 icon-md"></i>
                Tópicos: {{ $lesson->name }}
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        {{-- The top action bar remains the same --}}
        <div class="card shadow-sm border-0 mb-4 mt-4">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('dashboard.lessons.index') }}"
                        class="btn btn-outline-primary d-flex align-items-center">
                        <i data-feather="arrow-left" class="nav-icon me-2 icon-xs"></i>
                        Voltar
                    </a>
                    <h3 class="mb-0">Tópicos da Aula</h3>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="GET" action="{{ route('dashboard.lessons.topics.index', $lesson) }}" class="mb-0">
                        <div class="input-group">
                            <input type="search" class="form-control" name="q" value="{{ request('q') }}"
                                placeholder="Pesquisar tópicos...">
                            <button class="btn btn-primary" type="submit" title="Buscar"><i data-feather="search"
                                    class="icon-xs"></i></button>
                        </div>
                    </form>
                    <a href="{{ route('dashboard.lessons.topics.create', $lesson) }}" class="btn btn-primary text-nowrap">
                        <i data-feather="plus" class="nav-icon icon-xs"></i>
                        <span class="ms-1">Novo Tópico</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="list-group list-group-flush">
                @forelse ($topics as $topic)
                    <div class="list-group-item p-3">
                        <div class="d-flex align-items-center">
                            <a href="{{ asset('storage/' . $topic->video->path) }}" data-fancybox
                                class="d-none d-md-block flex-shrink-0 me-3">
                                <img src="{{ asset('storage/' . $topic->video->thumbnail_path) }}"
                                    style="width: 160px; height: 90px; object-fit: cover;" class="rounded"
                                    alt="Thumbnail">
                            </a>
                            <div class="flex-grow-1">
                                <h4 class="fw-bold mb-1">{{ $loop->iteration }}. {{ $topic->title }}</h4>
                                <p class="text-muted small mb-2">
                                    {!! Str::limit(strip_tags($topic->description), 120) !!}
                                </p>
                                <div class="d-flex align-items-center gap-4 small text-muted">
                                    <span title="Duração do Vídeo">
                                        <i data-feather="play-circle" class="icon-sm me-1"></i>
                                        {{ $topic->video->duration ?? 'N/A' }}
                                    </span>
                                    <span title="Anexos">
                                        <i data-feather="paperclip" class="icon-sm me-1"></i>
                                        {{ collect($topic->attachments)->count() }} Anexos
                                    </span>
                                    <span title="Questões">
                                        <i data-feather="help-circle" class="icon-sm me-1"></i>
                                        {{ collect($topic->quizzes)->count() }} Questões
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex gap-1 ms-3">
                                <a href="{{ route('dashboard.lessons.topics.edit', [$lesson->id, $topic->id]) }}"
                                    class="btn btn-ghost btn-icon btn-sm rounded-circle" title="Editar">
                                    <i data-feather="edit" class="icon-xs"></i>
                                </a>
                                <button type="button"
                                    class="btn btn-ghost btn-icon btn-sm rounded-circle text-danger"
                                    onclick="confirmDelete('delete-item-{{ $topic->id }}')" title="Apagar">
                                    <i data-feather="trash-2" class="icon-xs"></i>
                                </button>
                                <form class="d-none" id="delete-item-{{ $topic->id }}" method="POST"
                                    action="{{ route('dashboard.lessons.topics.destroy', [$lesson->id, $topic->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item p-5 text-center">
                        <i data-feather="inbox" class="icon-xl text-muted"></i>
                        <h4 class="text-muted mt-3">Nenhum tópico encontrado</h4>
                        <p class="text-muted mb-0">Adicione o primeiro tópico para esta aula.</p>
                    </div>
                @endforelse
            </div>
            @if ($topics->isNotEmpty() && $topics->hasPages())
                <div class="card-footer bg-transparent border-0">
                    {{ $topics->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection