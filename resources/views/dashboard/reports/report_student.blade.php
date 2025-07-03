@extends('templates.dashboard')

@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="mb-5 p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="pie-chart" class="nav-icon me-2 icon-md"></i>
                Relatório por Estudante
            </h1>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-12 d-grid d-lg-block ms-auto text-start">
                                <h3 class="mb-0">Histórico de Estudantes</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <form method="GET" action="{{ route('dashboard.reports.students') }}" class="mb-3">
                                    <div class="text-center">
                                        <label for="student_id" class="form-label">Estudante</label>
                                    </div>
                                    <select id="user-select" class="form-control" name="student_id" required>
                                        @if (isset($student))
                                            <option value="{{ $student->id }}" selected>{{ $student->name }}</option>
                                        @endif
                                    </select>
                                    <div class="text-center">
                                        <small>Pesquise e selecione um estudante do sistema</small>
                                        @error('student_id')
                                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3 mt-4 text-center">
                                        <button type="submit" class="btn btn-primary btn-md">Visualizar Relatório</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4"></div>
                        </div>
                        @if ($student)
                            <div class="row mb-5">
                                <div class="col-md-12 text-center mb-4">
                                    <a href="{{ $student->file ? asset('storage/' . $student->file->path) : asset('images/default_user.png') }}"
                                        data-fancybox>
                                        <img class="avatar-xl avatar rounded-circle"
                                            src="{{ $student->file ? asset('storage/' . $student->file->path) : asset('images/default_user.png') }}"
                                            alt="Foto do usuário">
                                    </a>
                                    <h3 class="mb-0">{{ $student->name }}</h3>
                                    <h5 class="mb-3">{{ $student->email }}</h5>
                                </div>
                            </div>
                            <div class="table-responsive table-card">
                                <table class="table mb-0 text-nowrap table-centered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Aula</th>
                                            <th class="text-center">Data de Início</th>
                                            <th class="text-center">Conclusão</th>
                                            <th class="text-center">Data de Conclusão</th>
                                            <th class="text-center">Nota</th>
                                            <th class="text-center">Emitiu Certificado?</th>
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
                                                                <p class="mb-0">{{ $lesson->specialty->name }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ $lesson->pivot->created_at_formatted }}</td>
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
                        @else
                            <p class="text-center text-muted">Selecione um estudante para ver o relatório.</p>
                        @endif
                    </div>

                    <div class="card-footer">
                        @if ($subscriptions->count())
                            {{ $subscriptions->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
