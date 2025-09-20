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
                <div class="card p-4 shadow-sm">
                    <form method="GET" action="{{ route('dashboard.reports.students') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="user-select" class="form-label fw-semibold">Estudante</label>
                                <select id="user-select" name="student_id" class="form-control" required
                                    placeholder="Digite para buscar..." autocomplete="off">
                                    @if (isset($student))
                                        <option value="{{ $student->id }}" selected>{{ $student->name }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Filtrar por Período de Início</label>
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

        @if ($student)
            <div class="row justify-content-center">
                <div class="col-lg-10 mb-5">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Detalhes do Estudante</h3>
                            <a href="{{ route('dashboard.reports.students.export', request()->query()) }}" target="_blank"
                                class="btn btn-primary">
                                <i data-feather="download" class="icon-xs me-1"></i>Exportar Relatório
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center">
                                    <img src="{{ $student->file ? asset('storage/' . $student->file->path) : asset('images/default_user.png') }}"
                                        alt="Foto do Estudante" class="img-fluid rounded-circle"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="col-md-9">
                                    <h2 class="mb-3">{{ $student->name }}</h2>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2 d-flex">
                                                    <i data-feather="at-sign" class="icon-sm me-2 text-muted"></i>
                                                    <span>{{ $student->email }}</span>
                                                </li>
                                                <li class="mb-2 d-flex">
                                                    <i data-feather="hash" class="icon-sm me-2 text-muted"></i>
                                                    <span>{{ $student->cpf ?? 'CPF não informado' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2 d-flex">
                                                    <i data-feather="calendar" class="icon-sm me-2 text-muted"></i>
                                                    <span>Cadastrado em:
                                                        <strong>{{ $student->created_at->format('d/m/Y') }}</strong></span>
                                                </li>
                                                <li class="mb-2 d-flex">
                                                    <i data-feather="log-in" class="icon-sm me-2 text-muted"></i>
                                                    <span>Último Acesso:
                                                        <strong>{{ $student->lastLogin?->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</strong></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top">
                            <div class="row g-3">
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-primary text-primary rounded-2 me-3"><i
                                                data-feather="book-open"></i></div>
                                        <div>
                                            <h4 class="mb-0">{{ $student->subscriptions_count }}</h4>
                                            <p class="mb-0 text-muted">Aulas Inscritas</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-success text-success rounded-2 me-3"><i
                                                data-feather="check-circle"></i></div>
                                        <div>
                                            <h4 class="mb-0">{{ $student->completed_subscriptions_count }}</h4>
                                            <p class="mb-0 text-muted">Aulas Concluídas</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-warning text-warning rounded-2 me-3"><i
                                                data-feather="clock"></i></div>
                                        <div>
                                            <h4 class="mb-0">{{ $student->pending_subscriptions_count }}</h4>
                                            <p class="mb-0 text-muted">Aulas Pendentes</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-lg bg-light-info text-info rounded-2 me-3"><i
                                                data-feather="award"></i></div>
                                        <div>
                                            <h4 class="mb-0">{{ $completedWorkload ?? 0 }}h</h4>
                                            <p class="mb-0 text-muted">Carga Horária</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Lesson History Card --}}
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
                                            <th class="text-center">Data de Início</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Data de Conclusão</th>
                                            <th class="text-center">Nota</th>
                                            <th class="text-center">Certificado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($subscriptions as $lesson)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <img class="avatar-md avatar rounded-circle"
                                                                src="{{ $lesson->file?->path ? asset('storage/' . $lesson->file->path) : asset('images/default_lesson.png') }}"
                                                                alt="{{ $lesson->name }}">
                                                        </div>
                                                        <div class="ms-3 lh-1">
                                                            <h5 class="mb-1">{{ $lesson->name }}</h5>
                                                            <p class="mb-0">{{ $lesson->teacher?->name }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $lesson->pivot->created_at->format('d/m/Y') }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($lesson->pivot->finished)
                                                        <span class="badge bg-success"><i data-feather="check-circle"
                                                                class="icon-xs me-1"></i>Concluído</span>
                                                    @else
                                                        <span class="badge bg-warning"><i data-feather="clock"
                                                                class="icon-xs me-1"></i>Em Andamento</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ $lesson->pivot->finished_at ? \Carbon\Carbon::parse($lesson->pivot->finished_at)->format('d/m/Y') : '-' }}
                                                </td>
                                                <td class="text-center fw-bold">{{ $lesson->pivot->score ?? '-' }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $hasCertificate = $student->certificates->firstWhere(
                                                            'lesson_id',
                                                            $lesson->id,
                                                        );
                                                    @endphp
                                                    @if ($hasCertificate)
                                                        <span class="badge bg-info"><i data-feather="award"
                                                                class="icon-xs me-1"></i>Emitido</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted">Nenhuma aula
                                                    encontrada para este estudante no período selecionado.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($subscriptions->isNotEmpty())
                            <div class="card-footer border-0">
                                {{ $subscriptions->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="text-center mt-8">
                <i data-feather="users" class="icon-xl text-muted"></i>
                <h4 class="text-muted mt-2">Selecione um estudante para visualizar o relatório.</h4>
            </div>
        @endif
    </div>
@endsection
