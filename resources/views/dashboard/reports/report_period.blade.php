@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;
@endphp

@extends('templates.dashboard')
@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="pie-chart" class="nav-icon me-2 icon-md"></i>
                Relatório de Conclusão por Período
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row mb-4 justify-content-center">
            <div class="col-lg-10">
                <div class="card p-4 shadow-sm">
                    <form method="GET" action="{{ route('dashboard.reports.periods') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-9">
                                <label class="form-label fw-semibold">Filtrar por Período de Conclusão</label>
                                <div class="input-group">
                                    <span class="input-group-text">De</span>
                                    <input type="date" name="start_date"
                                        class="form-control @if ($errors->has('start_date') || $errors->has('end_date')) is-invalid @endif"
                                        value="{{ $filter['start_date'] ?? '' }}" required>

                                    <span class="input-group-text">Até</span>
                                    <input type="date" name="end_date"
                                        class="form-control @if ($errors->has('start_date') || $errors->has('end_date')) is-invalid @endif"
                                        value="{{ $filter['end_date'] ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i data-feather="search" class="icon-xs"></i>
                                    <span class="ms-1">Gerar Relatório</span>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                @error('start_date')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @else
                                    @error('end_date')
                                        <div class="text-danger mt-1 small">{{ $message }}</div>
                                    @enderror
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if (request()->filled(['start_date', 'end_date']))
            <div class="row justify-content-center mb-4">
                <div class="col-lg-10">
                    <div class="card shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">
                                    Resultados de
                                    <span class="fw-bold">{{ Carbon::parse($filter['start_date'])->format('d/m/Y') }}</span>
                                    a
                                    <span class="fw-bold">{{ Carbon::parse($filter['end_date'])->format('d/m/Y') }}</span>
                                </h5>
                            </div>
                            <div>
                                <a href="{{ route('dashboard.reports.periods.export', ['start_date' => $filter['start_date'], 'end_date' => $filter['end_date']]) }}"
                                    class="btn btn-primary">
                                    <i data-feather="download" class="icon-xs me-1"></i> Exportar para PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($lessons->isNotEmpty())
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card shadow-sm">
                                    <div class="card-header">
                                        <h4 class="mb-0">Aulas Encontradas ({{ $lessons->count() }})</h4>
                                    </div>
                                    <div class="list-group list-group-flush" id="lesson-list-tab" role="tablist"
                                        style="max-height: 70vh; overflow-y: auto;">
                                        @foreach ($lessons as $lesson)
                                            <a class="list-group-item list-group-item-action @if ($loop->first) active @endif"
                                                id="list-{{ $lesson->id }}-list" data-bs-toggle="list"
                                                href="#list-{{ $lesson->id }}" role="tab"
                                                aria-controls="list-{{ $lesson->id }}">
                                                <span class="fs-4 fw-semibold">{{ $loop->iteration }}.
                                                    {{ $lesson->name }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="tab-content" id="nav-tabContent">
                                    @foreach ($lessons as $lesson)
                                        <div class="tab-pane fade @if ($loop->first) show active @endif"
                                            id="list-{{ $lesson->id }}" role="tabpanel"
                                            aria-labelledby="list-{{ $lesson->id }}-list">
                                            <div class="card shadow-sm">
                                                <div class="card-header p-3 bg-white">
                                                    <div class="d-flex w-100 align-items-center">
                                                        <div class="flex-shrink-0 me-4">
                                                            <img src="{{ $lesson->file?->path ? asset('storage/' . $lesson->file->path) : asset('images/default_lesson.png') }}"
                                                                alt="Capa" class="rounded-3"
                                                                style="width: 200px; height: 140px; object-fit: cover;">
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                                @foreach ($lesson->specialties as $specialty)
                                                                    <span class="badge bg-secondary"><i
                                                                            class="bi bi-tag me-1"></i>{{ $specialty->name }}</span>
                                                                @endforeach
                                                            </div>
                                                            <h1 class="mb-1">{{ $lesson->name }}</h1>
                                                            <p class="text-muted mb-0 fs-5"><i data-feather="briefcase"
                                                                    class="icon-xs me-1"></i> Professor(a):
                                                                {{ $lesson->teacher?->name ?? 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <h5 class="mb-3"><i data-feather="list" class="icon-sm me-1"></i>
                                                        Tópicos da Aula</h5>
                                                    @if ($lesson->topics->isNotEmpty())
                                                        <div class="accordion" id="topicsAccordion-{{ $lesson->id }}">
                                                            @foreach ($lesson->topics as $topic)
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header"
                                                                        id="heading-topic-{{ $topic->id }}">
                                                                        <button
                                                                            class="accordion-button collapsed fw-semibold"
                                                                            type="button" data-bs-toggle="collapse"
                                                                            data-bs-target="#collapse-topic-{{ $topic->id }}">
                                                                            {{ $topic->title }}
                                                                        </button>
                                                                    </h2>
                                                                    <div id="collapse-topic-{{ $topic->id }}"
                                                                        class="accordion-collapse collapse"
                                                                        data-bs-parent="#topicsAccordion-{{ $lesson->id }}">
                                                                        <div class="accordion-body small text-muted">
                                                                            {!! strip_tags($topic->description) !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-center text-muted p-3">
                                                            Nenhum tópico cadastrado.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="card-body border-top">
                                                    <h5 class="mb-3"><i data-feather="users" class="icon-sm me-1"></i>
                                                        Alunos Concluintes no Período</h5>
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-striped mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th class="ps-4"><i data-feather="user"
                                                                            class="icon-xs me-1"></i>Estudante</th>
                                                                    <th class="text-center"><i data-feather="calendar"
                                                                            class="icon-xs me-1"></i>Início</th>
                                                                    <th class="text-center"><i data-feather="check-square"
                                                                            class="icon-xs me-1"></i>Conclusão</th>
                                                                    <th class="text-center"><i data-feather="star"
                                                                            class="icon-xs me-1"></i>Nota</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($lesson->subscriptions as $student)
                                                                    <tr>
                                                                        <td class="ps-4">
                                                                            <div class="d-flex align-items-center">
                                                                                <img class="avatar avatar-sm rounded-circle"
                                                                                    src="{{ $student->file?->path ? asset('storage/' . $student->file->path) : 'https://placehold.co/40x40/EBF4FF/7F9CF5?text=' . strtoupper(substr($student->name, 0, 1)) }}"
                                                                                    alt="Foto do Aluno">
                                                                                <div class="ms-3 lh-1">
                                                                                    <h5 class="mb-1 fs-6">
                                                                                        {{ $student->name }}</h5>
                                                                                    <p class="mb-0 small text-muted">
                                                                                        CPF: {{ $student->cpf }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{ Carbon::parse($student->pivot->created_at)->format('d/m/Y') }}
                                                                        </td>
                                                                        <td class="text-center">
                                                                            {{ Carbon::parse($student->pivot->finished_at)->format('d/m/Y') }}
                                                                        </td>
                                                                        <td class="text-center fw-bold">
                                                                            @if (isset($student->pivot->score))
                                                                                {{ $student->pivot->score }}
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center p-5 card shadow-sm col-lg-10 mx-auto">
                    <i data-feather="user-x" class="icon-xl text-muted"></i>
                    <p class="mt-2 mb-0 text-muted">Nenhum aluno concluiu aulas no período selecionado.</p>
                </div>
            @endif
        @else
            <div class="text-center mt-8">
                <i data-feather="search" class="icon-xl text-muted"></i>
                <h4 class="text-muted mt-2">Selecione um período para gerar o relatório.</h4>
            </div>
        @endif
    </div>
@endsection
