@php
    use App\Enums\LessonStatusEnum;
@endphp

@extends('templates.dashboard')
@section('content')
    {{-- Page Title --}}
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="briefcase" class="nav-icon me-2 icon-md"></i>
                Relatório por Professor
            </h1>
        </div>
    </div>

    <div class="container-fluid">
        {{-- ✅ Filter Section with Date Inputs --}}
        <div class="row mb-4 justify-content-center">
            <div class="col-lg-10">
                <div class="card p-4 shadow-sm">
                    <form method="GET" action="{{ route('dashboard.reports.teachers') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="professor-select" class="form-label fw-semibold">Professor</label>
                                <select id="professor-select" class="form-control" name="teacher_id" required
                                    placeholder="Digite para buscar..." autocomplete="off">
                                    @if (isset($teacher))
                                        <option value="{{ $teacher->id }}" selected>{{ $teacher->name }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Filtrar por Período de Criação da Aula</label>
                                <div class="input-group">
                                    <span class="input-group-text">De</span>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                    <span class="input-group-text">Até</span>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i data-feather="search" class="icon-xs"></i>
                                    <span class="ms-1">Filtrar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($teacher)
            <div class="row justify-content-center">
                <div class="col-lg-10 mb-5">
                    
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Informações do Professor</h3>
                            <a href="{{ route('dashboard.reports.teachers.export', request()->query()) }}" target="_blank" class="btn btn-primary">
                                <i data-feather="download" class="icon-xs me-1"></i>Exportar Relatório
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-2 text-center">
                                    <img src="{{ $teacher->file?->path ? asset('storage/' . $teacher->file->path) : asset('images/default_user.png') }}"
                                        alt="Foto do Professor" class="img-fluid rounded-circle"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="col-md-10">
                                    <h2 class="mb-1">{{ $teacher->name }}</h2>
                                    <p class="text-muted mb-1">
                                        <i data-feather="mail" class="icon-xs me-1"></i>
                                        {{ $teacher->email }}
                                    </p>
                                     <p class="text-muted mb-0">
                                        <i data-feather="award" class="icon-xs me-1"></i>
                                        Especialidade: {{ $teacher->expertise ?? 'Não informada' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top">
                            <div class="row g-3">
                                <div class="col-md col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-primary text-primary rounded-2 me-3"><i data-feather="users"></i></div>
                                        <div><h4 class="mb-0">{{ $stats['total_students'] }}</h4><p class="mb-0 text-muted">Alunos</p></div>
                                    </div>
                                </div>
                                <div class="col-md col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-secondary text-secondary rounded-2 me-3"><i data-feather="book-open"></i></div>
                                        <div><h4 class="mb-0">{{ $stats['created_lessons_count'] }}</h4><p class="mb-0 text-muted">Aulas Criadas</p></div>
                                    </div>
                                </div>
                                <div class="col-md col-6">
                                     <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-success text-success rounded-2 me-3"><i data-feather="check-circle"></i></div>
                                        <div><h4 class="mb-0">{{ $stats['status_counts'][LessonStatusEnum::PUBLICADA->value] ?? 0 }}</h4><p class="mb-0 text-muted">Publicadas</p></div>
                                    </div>
                                </div>
                                <div class="col-md col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-warning text-warning rounded-2 me-3"><i data-feather="edit-3"></i></div>
                                        <div><h4 class="mb-0">{{ $stats['status_counts'][LessonStatusEnum::RASCUNHO->value] ?? 0 }}</h4><p class="mb-0 text-muted">Rascunhos</p></div>
                                    </div>
                                </div>
                                 <div class="col-md col-12">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-info text-info rounded-2 me-3"><i data-feather="clock"></i></div>
                                        <div><h4 class="mb-0">{{ $stats['status_counts'][LessonStatusEnum::AGUARDANDO_PUBLICACAO->value] ?? 0 }}</h4><p class="mb-0 text-muted">Aguardando</p></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4 shadow-sm">
                        <div class="card-header">
                            <h3 class="details-title mb-0">Histórico de Aulas</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0 text-nowrap table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Aula</th>
                                            <th class="text-center">Data de Criação</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Tópicos</th>
                                            <th class="text-center">Carga Horária</th>
                                            <th class="text-center">Alunos Inscritos</th>
                                            <th class="text-center">Média das Notas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lessons as $lesson)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <img class="avatar-md avatar rounded-circle" src="{{ $lesson->file?->path ? asset('storage/' . $lesson->file->path) : asset('images/default_lesson.png') }}" alt="{{ $lesson->name }}">
                                                        </div>
                                                        <div class="ms-3 lh-1">
                                                            <h5 class="mb-1">{{ $lesson->name }}</h5>
                                                            <p class="mb-0">{{ $lesson->specialty->name ?? '' }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $lesson->created_at->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    @if ($lesson->lesson_status === LessonStatusEnum::RASCUNHO->value)
                                                        <span class="badge bg-warning"><i data-feather="edit-3" class="icon-xs me-1"></i>Rascunho</span>
                                                    @elseif ($lesson->lesson_status === LessonStatusEnum::AGUARDANDO_PUBLICACAO->value)
                                                        <span class="badge bg-info"><i data-feather="clock" class="icon-xs me-1"></i>Aguardando</span>
                                                    @else
                                                        <span class="badge bg-success"><i data-feather="check-circle" class="icon-xs me-1"></i>Publicada</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ count($lesson->topics) }}</td>
                                                <td class="text-center">{{ $lesson->workload }}h</td>
                                                <td class="text-center">{{ $lesson->subscriptions_count }}</td>
                                                <td class="text-center">{{ $lesson->average_score ? number_format($lesson->average_score, 1) : '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">Nenhuma aula encontrada para este professor no período selecionado.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($lessons->isNotEmpty())
                            <div class="card-footer border-0">
                                {{ $lessons->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
             <div class="text-center mt-8">
                <i data-feather="users" class="icon-xl text-muted"></i>
                <h4 class="text-muted mt-2">Selecione um professor para ver o relatório.</h4>
            </div>
        @endif
    </div>
@endsection