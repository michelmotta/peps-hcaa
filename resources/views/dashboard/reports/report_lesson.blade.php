@php
    use App\Enums\LessonStatusEnum;
    use Carbon\Carbon;
@endphp

@extends('templates.dashboard')

@section('content')
    {{-- Page Title --}}
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="book-open" class="nav-icon me-2 icon-md"></i>
                Relatório de Aula
            </h1>
        </div>
    </div>

    <div class="container-fluid">
        {{-- Filter Section --}}
        <div class="row mb-4 justify-content-center">
            <div class="col-lg-10">
                <div class="card p-4 shadow-sm">
                    <form method="GET" action="{{ route('dashboard.reports.lessons') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-9">
                                <label for="lesson-select" class="form-label fw-semibold">Aula</label>
                                <select id="lesson-select" class="form-control" name="lesson_id" required
                                    placeholder="Digite para buscar pela aula..." autocomplete="off">
                                    @if (isset($lesson))
                                        <option value="{{ $lesson->id }}" selected>{{ $lesson->name }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i data-feather="search" class="icon-xs"></i>
                                    <span class="ms-1">Buscar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if (isset($lesson))
            <div class="row justify-content-center">
                <div class="col-lg-10 mb-5">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Detalhes da Aula</h3>
                            <a href="{{ route('dashboard.reports.lessons.export', ['lesson_id' => $lesson->id]) }}"
                                target="_blank" class="btn btn-primary">
                                <i data-feather="download" class="icon-xs me-1"></i>Exportar Relatório
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <img src="{{ $lesson->file?->path ? asset('storage/' . $lesson->file->path) : asset('images/default_lesson.png') }}"
                                        alt="Capa da Aula" class="img-fluid rounded-circle"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="col-md-10">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="d-flex flex-wrap gap-2 mb-2">
                                                @foreach ($lesson->specialties as $specialty)
                                                    <a href="#"
                                                        class="badge bg-light text-dark text-decoration-none fw-normal">
                                                        <i class="bi bi-tag me-1"></i>
                                                        {{ $specialty->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                            <h2 class="mb-1">{{ $lesson->name }}</h2>
                                            <p class="text-muted mb-1">
                                                <i data-feather="briefcase" class="icon-xs me-1"></i>
                                                Professor: {{ $lesson->teacher?->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                        <div>
                                            @if ($lesson->lesson_status === LessonStatusEnum::PUBLICADA->value)
                                                <span class="badge bg-success fs-6"><i data-feather="check-circle"
                                                        class="icon-xs me-1"></i> Publicada</span>
                                            @elseif ($lesson->lesson_status === LessonStatusEnum::RASCUNHO->value)
                                                <span class="badge bg-warning fs-6"><i data-feather="edit-3"
                                                        class="icon-xs me-1"></i> Rascunho</span>
                                            @else
                                                <span class="badge bg-info fs-6"><i data-feather="clock"
                                                        class="icon-xs me-1"></i> Aguardando</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                    <p class="mb-0">{{ $lesson->description }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top">
                            <div class="row g-3">
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-primary text-primary rounded-2 me-3"><i
                                                data-feather="users"></i></div>
                                        <div>
                                            <h4 class="mb-0">{{ $lesson->subscriptions_count }}</h4>
                                            <p class="mb-0 text-muted">Alunos</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-success text-success rounded-2 me-3"><i
                                                data-feather="check-circle"></i></div>
                                        <div>
                                            <h4 class="mb-0">{{ $lesson->completed_subscriptions_count }}</h4>
                                            <p class="mb-0 text-muted">Concluíram</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-warning text-warning rounded-2 me-3"><i
                                                data-feather="star"></i></div>
                                        <div>
                                            <h4 class="mb-0">{{ number_format($lesson->average_score, 1) }}</h4>
                                            <p class="mb-0 text-muted">Nota Média</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-info text-info rounded-2 me-3"><i
                                                data-feather="clock"></i></div>
                                        <div>
                                            <h4 class="mb-0">{{ $lesson->workload }}h</h4>
                                            <p class="mb-0 text-muted">Carga Horária</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Topics Accordion --}}
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="details-title mb-0">
                                <i data-feather="list" class="icon-sm me-2"></i>Tópicos da Aula
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="accordion" id="topicsAccordion">
                                @forelse ($lesson->topics as $topic)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ $topic->id }}">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $topic->id }}"
                                                aria-expanded="false" aria-controls="collapse{{ $topic->id }}">
                                                <strong>{{ $topic->title }}</strong>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $topic->id }}" class="accordion-collapse collapse"
                                            aria-labelledby="heading{{ $topic->id }}"
                                            data-bs-parent="#topicsAccordion">
                                            <div class="accordion-body">
                                                {{ $topic->description }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4 text-muted">Nenhum tópico cadastrado para esta aula.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Student List Card --}}
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="details-title mb-0">
                                <i data-feather="users" class="icon-sm me-2"></i>Alunos Inscritos
                            </h3>
                            {{-- Button was removed from here --}}
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0 text-nowrap table-centered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Estudante</th>
                                            <th scope="col" class="text-center">Data de Início</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col" class="text-center">Data de Conclusão</th>
                                            <th scope="col" class="text-center">Nota</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($students as $student)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <img class="avatar-md avatar rounded-circle"
                                                                src="{{ $student->file?->path ? asset('storage/' . $student->file->path) : asset('images/default_user.png') }}"
                                                                alt="Foto do Aluno">
                                                        </div>
                                                        <div class="ms-3 lh-1">
                                                            <h5 class="mb-1">{{ $student->name }}</h5>
                                                            <p class="mb-0">{{ $student->email }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    {{ $student->pivot->created_at->format('d/m/Y') }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($student->pivot->finished)
                                                        <span class="badge bg-success"><i data-feather="check-circle"
                                                                class="icon-xs me-1"></i>Concluído</span>
                                                    @else
                                                        <span class="badge bg-warning"><i data-feather="clock"
                                                                class="icon-xs me-1"></i>Em Andamento</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ $student->pivot->finished_at ? Carbon::parse($student->pivot->finished_at)->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="text-center fw-bold">
                                                    {{ $student->pivot->score ?? '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4 text-muted">Nenhum aluno
                                                    inscrito nesta aula.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($students->isNotEmpty())
                            <div class="card-footer border-0">
                                {{ $students->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="text-center mt-8">
                <i data-feather="search" class="icon-xl text-muted"></i>
                <h4 class="text-muted mt-2">Selecione uma aula para ver o relatório detalhado.</h4>
            </div>
        @endif
    </div>
@endsection
