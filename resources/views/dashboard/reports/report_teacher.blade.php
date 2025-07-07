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
        <div class="row mb-4 justify-content-center">
            <div class="col-lg-10">
                <div class="card p-4 shadow-sm">
                    <form method="GET" action="{{ route('dashboard.reports.teachers') }}">
                        <div class="row g-3 align-items-end">

                            {{-- Teacher Selector --}}
                            <div class="col-md-5">
                                <label for="professor-select" class="form-label fw-semibold">Professor</label>
                                <select id="professor-select" class="form-control" name="teacher_id" required
                                    placeholder="Digite para buscar..." autocomplete="off">
                                    @if (isset($teacher))
                                        <option value="{{ $teacher->id }}" selected>{{ $teacher->name }}</option>
                                    @endif
                                </select>
                            </div>

                            {{-- Date Range Filter --}}
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Filtrar por Período de Criação da Aula</label>
                                <div class="input-group">
                                    <span class="input-group-text">De</span>
                                    <input type="date" id="start_date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                    <span class="input-group-text">Até</span>
                                    <input type="date" id="end_date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                            </div>

                            {{-- Submit Button --}}
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
                    <div class="profile-card bg-primary">
                        <div class="profile-body text-center">
                            <img src="{{ $teacher->file ? asset('storage/' . $teacher->file->path) : asset('images/default_user.png') }}"
                                alt="Foto" class="profile-photo">
                            <h2 class="profile-name">{{ $teacher->name }}</h2>
                            <p class="profile-email mb-4">{{ $teacher->email }}</p>
                            <hr>
                            <h5 class="section-title mt-4">Estatísticas Gerais</h5>
                            <div class="row g-3">
                                <div class="col">
                                    <div class="stat-card"><i data-feather="users"></i><span
                                            class="stat-count">{{ $stats['total_students'] }}</span><span
                                            class="stat-label">Alunos</span></div>
                                </div>
                                <div class="col">
                                    <div class="stat-card">
                                        <i data-feather="book-open"></i>
                                        <span class="stat-count">{{ $stats['created_lessons_count'] }}</span>
                                        <span class="stat-label">Aulas Criadas</span>
                                    </div>
                                </div>

                                <div class="col">
                                    <div class="stat-card"><i data-feather="edit-3"></i><span
                                            class="stat-count">{{ $stats['status_counts'][LessonStatusEnum::RASCUNHO->value] ?? 0 }}</span><span
                                            class="stat-label">Aulas em Rascunho</span></div>
                                </div>
                                <div class="col">
                                    <div class="stat-card"><i data-feather="pause-circle"></i><span
                                            class="stat-count">{{ $stats['status_counts'][LessonStatusEnum::AGUARDANDO_PUBLICACAO->value] ?? 0 }}</span><span
                                            class="stat-label">Aulas Aguardando</span></div>
                                </div>
                                <div class="col">
                                    <div class="stat-card"><i data-feather="globe"></i><span
                                            class="stat-count">{{ $stats['status_counts'][LessonStatusEnum::PUBLICADA->value] ?? 0 }}</span><span
                                            class="stat-label">Aulas Publicadas</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="details-title mb-0">Histórico de Aulas</h3>
                            <a href="{{ route('dashboard.reports.teachers.export', request()->query()) }}" target="_blank"
                                class="btn btn-outline-primary">
                                <i data-feather="download" class="icon-xs me-1"></i>Exportar PDF
                            </a>
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
                                                            <a href="{{ asset('storage/' . $lesson->file->path) }}"
                                                                data-fancybox>
                                                                <img class="avatar-md avatar rounded-circle"
                                                                    src="{{ asset('storage/' . $lesson->file->path) }}"
                                                                    alt="{{ $lesson->name }}">
                                                            </a>
                                                        </div>
                                                        <div class="ms-3 lh-1">
                                                            <h5 class="mb-1">{{ $lesson->name }}</h5>
                                                            <p class="mb-0">{{ $lesson->specialty->name ?? '' }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $lesson->created_at_formatted }}</td>
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
                                                <td class="text-center">{{ $lesson->topics->count() }}</td>
                                                <td class="text-center">{{ $lesson->workload }}</td>
                                                <td class="text-center">{{ $lesson->subscriptions_count }}</td>
                                                <td class="text-center">
                                                    {{ $lesson->average_score ? number_format($lesson->average_score, 1) : '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4 text-muted">Nenhuma aula
                                                    encontrada para este professor.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer border-0">
                            @if ($lessons->isNotEmpty())
                                {{ $lessons->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
