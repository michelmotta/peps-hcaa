@extends('templates.dashboard')

@section('content')
    <div class="bg-primary rounded-3 mt-n6 mx-n4">
        <div class="mb-5 p-10">
            <h1 class="mb-0 text-white text-center">
                <i data-feather="pie-chart" class="nav-icon me-2 icon-md"></i>
                Relatório por Aula
            </h1>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 mb-5">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="mb-0">Histórico de Aulas</h3>
                    </div>

                    <div class="card-body">
                        <div class="row mb-5">
                            <div class="col-md-4 offset-md-4">
                                <form method="GET" action="{{ route('dashboard.reports.lessons') }}">
                                    <div class="text-center mb-2">
                                        <label for="lesson_id" class="form-label">Aula</label>
                                    </div>
                                    <select id="lesson-select" class="form-control" name="lesson_id" required>
                                        @if (isset($lesson))
                                            <option value="{{ $lesson->id }}" selected>{{ $lesson->name }}</option>
                                        @endif
                                    </select>
                                    <div class="text-center mt-1">
                                        <small>Pesquise e selecione uma aula</small>
                                        @error('lesson_id')
                                            <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3 mt-4 text-center">
                                        <button type="submit" class="btn btn-primary btn-md">Visualizar Relatório</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @if ($lesson)
                            <div class="row mb-6">
                                <div class="col-md-12 text-center mb-4">
                                    @if ($lesson->file && $lesson->file->path)
                                        <img src="{{ asset('storage/' . $lesson->file->path) }}"
                                            alt="Thumbnail da aula {{ $lesson->name }}" class="img-thumbnail mb-3"
                                            style="max-width: 200px;">
                                    @endif
                                    <h3 class="mb-0">{{ $lesson->name }}</h3>
                                    <h5 class="mb-3">{{ $lesson->teacher->name }}</h5>
                                </div>
                            </div>
                            <div class="table-responsive table-card">
                                <table class="table mb-0 text-nowrap table-centered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Estudante</th>
                                            <th class="text-center">Data de Início</th>
                                            <th class="text-center">Conclusão</th>
                                            <th class="text-center">Data de Conclusão</th>
                                            <th class="text-center">Nota</th>
                                            <th class="text-center">Emitiu Certificado?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($subscriptions as $subscription)
                                            @php
                                                $certificate = $lesson->certificates->firstWhere(
                                                    'user_id',
                                                    $subscription->id,
                                                );
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $subscription->file ? asset('storage/' . $subscription->file->path) : asset('images/default_user.png') }}"
                                                            class="avatar-md avatar rounded-circle me-2"
                                                            alt="{{ $subscription->name }}">
                                                        <div>
                                                            <strong>{{ $subscription->name }}</strong><br>
                                                            <small>{{ $subscription->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    {{ $subscription->pivot->created_at->format('d/m/Y') }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($subscription->pivot->finished)
                                                        <span class="badge bg-success">
                                                            <i data-feather="check-circle" class="icon-xs me-1"></i>
                                                            Concluído
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning">
                                                            <i data-feather="clock" class="icon-xs me-1"></i> Em Andamento
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ $subscription->pivot->finished_at_formatted }}
                                                </td>
                                                <td class="text-center">
                                                    {{ $subscription->pivot->score ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    @if ($certificate)
                                                        <span class="badge bg-light text-dark">
                                                            <i data-feather="award" class="icon-xs me-1"></i>
                                                            Emitido em {{ $certificate->created_at->format('d/m/Y') }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-muted">Selecione uma aula para ver o relatório.</p>
                        @endif
                    </div>

                    <div class="card-footer">
                        @if ($subscriptions->isNotEmpty())
                            {{ $subscriptions->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
