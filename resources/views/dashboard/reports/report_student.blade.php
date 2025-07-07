@extends('templates.dashboard')

@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="pie-chart" class="nav-icon me-2 icon-md"></i>
                Relatório por Estudante
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row mb-4 justify-content-center">
            <div class="col-lg-10">
                <div class="card p-4 shadow-sm border">
                    <form method="GET" action="{{ route('dashboard.reports.students') }}">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <label for="user-select" class="form-label fw-semibold">Estudante</label>
                                <select id="user-select" name="student_id" class="form-control" required
                                    placeholder="Digite para buscar..." autocomplete="off">
                                    @if (isset($student))
                                        <option value="{{ $student->id }}" selected>{{ $student->name }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Filtrar por Período de Início</label>
                                <div class="input-group">
                                    <span class="input-group-text">De</span>
                                    <input type="date" id="start_date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                    <span class="input-group-text">Até</span>
                                    <input type="date" id="end_date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 mt-7">
                                    <i data-feather="search" class="icon-xs"></i>
                                    <span class="ms-1">Filtrar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if ($student)
            <div class="row justify-content-center mt-10">
                <div class="col-md-10 mb-5">
                    <div class="student-profile-card">
                        <div class="profile-header">
                            <div class="profile-header-overlay"></div>
                            <div class="profile-photo-wrapper">
                                <img src="{{ $student->file ? asset('storage/' . $student->file->path) : asset('images/default_user.png') }}"
                                    alt="Foto do Estudante" class="profile-photo">
                            </div>
                            <h3 class="profile-name">{{ $student->name }}</h3>
                            <p class="profile-email">{{ $student->email }}</p>
                        </div>
                        <div class="profile-body">
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <h5 class="section-title">Informações Gerais</h5>
                                    <ul class="info-list">
                                        <li>
                                            <i data-feather="user"></i>
                                            <span>Usuário</span>
                                            <strong>{{ $student->username ?? '-' }}</strong>
                                        </li>
                                        <li>
                                            <i data-feather="hash"></i>
                                            <span>CPF</span>
                                            <strong>{{ $student->cpf ?? '-' }}</strong>
                                        </li>
                                        <li>
                                            <i data-feather="at-sign"></i>
                                            <span>E-mail</span>
                                            <strong>{{ $student->email ?? '-' }}</strong>
                                        </li>
                                        <li>
                                            <i data-feather="calendar"></i>
                                            <span>Cadastrado em</span>
                                            <strong>{{ $student->created_at->format('d/m/Y') }}</strong>
                                        </li>
                                        <li>
                                            <i data-feather="log-in"></i>
                                            <span>Último Acesso</span>
                                            <strong>{{ $student->lastLogin?->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</strong>
                                        </li>
                                        <li>
                                            <i data-feather="toggle-right"></i>
                                            <span>Status</span>
                                            <span
                                                class="badge rounded-pill {{ $student->active ?? true ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}">
                                                {{ $student->active ?? true ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-lg-6">
                                    <h5 class="section-title">Atividade e Carga Horária</h5>
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <div class="stat-card h-100">
                                                <i data-feather="clock"></i>
                                                <span class="stat-count">{{ $completedWorkload ?? 0 }}h</span>
                                                <span class="stat-label">Carga Horária Cumprida</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="stat-card h-100">
                                                <i data-feather="log-in"></i>
                                                <span class="stat-count"
                                                    style="font-size: 1.1rem;">{{ $student->lastLogin?->created_at?->format('d/m/Y') ?? 'N/A' }}</span>
                                                <span class="stat-label">Último Acesso</span>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="section-title">Progresso de Aulas</h5>
                                    <div class="row g-3">
                                        <div class="col-4">
                                            <div class="stat-card">
                                                <i data-feather="book-open"></i>
                                                <span class="stat-count">{{ $student->subscriptions_count }}</span>
                                                <span class="stat-label">Totais</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-card">
                                                <i data-feather="check-circle"></i>
                                                <span
                                                    class="stat-count">{{ $student->completed_subscriptions_count }}</span>
                                                <span class="stat-label">Concluídas</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-card">
                                                <i data-feather="clock"></i>
                                                <span class="stat-count">{{ $student->pending_subscriptions_count }}</span>
                                                <span class="stat-label">Pendentes</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('dashboard.reports.students.export', request()->query()) }}"
                                            target="_blank" class="btn btn-primary w-100 export-btn">
                                            <i data-feather="download"></i>Exportar Relatório
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-10 mb-5">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-12 d-grid d-lg-block ms-auto text-start">
                                    <h3 class="mb-0">Histórico de Aulas</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table mb-0 text-nowrap table-centered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Aula</th>
                                            <th class="text-center">Carga Horária</th>
                                            <th class="text-center">Data de Início</th>
                                            <th class="text-center">Conclusão</th>
                                            <th class="text-center">Data de Conclusão</th>
                                            <th class="text-center">Nota</th>
                                            <th class="text-center">Certificado Emitido</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($subscriptions->isNotEmpty())
                                            @foreach ($subscriptions as $lesson)
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
                                                                <p class="mb-0">{{ $lesson->teacher->name }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $lesson->workload }}h
                                                    </td>
                                                    <td class="text-center">
                                                        {{ $lesson->pivot->created_at_formatted }}
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($lesson->pivot->finished)
                                                            <span class="badge bg-success">
                                                                <i data-feather="check-circle" class="icon-xs me-1"></i>
                                                                Concluído
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i data-feather="clock" class="icon-xs me-1"></i> Em
                                                                Andamento
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $lesson->pivot->finished_at_formatted }}
                                                    </td>
                                                    <td class="text-center">{{ $lesson->pivot->score ?? '-' }}</td>
                                                    <td class="text-center">
                                                        @php
                                                            $hasCertificate = $student->certificates->firstWhere(
                                                                'lesson_id',
                                                                $lesson->id,
                                                            );
                                                        @endphp

                                                        @if ($hasCertificate)
                                                            <span class="badge bg-light text-dark">
                                                                <i data-feather="award" class="icon-xs me-1"></i>
                                                                Emitido em
                                                                {{ $hasCertificate->created_at->format('d/m/Y') }}
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="7" class="text-center">Nenhuma aula encontrada para este
                                                    estudante.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <div class="card-footer">
                            @if ($subscriptions->count())
                                {{ $subscriptions->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <p class="text-center text-muted">Selecione um estudante para visualizar o relatório.</p>
        @endif
    </div>
@endsection
