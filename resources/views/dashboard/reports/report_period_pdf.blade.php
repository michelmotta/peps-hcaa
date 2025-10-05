<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Aulas por Período</title>
    <style>
        @page {
            margin: 100px 0 60px 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
        }

        .header {
            background-color: #133b6a;
            color: #FFFFFF;
            width: 100%;
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 50px;
            padding: 20px 30px;
            box-sizing: border-box;
        }

        .footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            text-align: center;
            font-size: 10px;
            color: #999;
            padding: 15px 30px;
            border-top: 1px solid #e0e0e0;
            background-color: white;
            box-sizing: border-box;
        }

        .footer .page-number:after {
            content: "Página " counter(page);
        }

        main {
            padding: 0 30px;
        }

        .header-content-table {
            width: 100%;
        }

        .header-logo-cell {
            width: 80px;
            vertical-align: middle;
        }

        .header-logo-cell img {
            max-height: 40px;
        }

        .header-text-cell {
            vertical-align: middle;
            padding-left: 20px;
        }

        .header-text-cell h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .header-text-cell p {
            font-size: 13px;
            margin: 4px 0 0 0;
            opacity: 0.9;
        }

        .document-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-period {
            text-align: center;
            padding-bottom: 15px;
            font-size: 12px;
            color: #6c757d;
        }

        .lesson-card {
            background-color: #ffffff;
            padding: 20px;
            margin-bottom: 25px;
            border-radius: 5px;
            border-bottom: 3px solid #133b6a;
        }

        .lesson-card:last-child {
            border-bottom: none;
        }

        .lesson-card h2 {
            font-size: 20px;
            margin: 0 0 2px 0;
            color: #133b6a;
        }

        .lesson-card .professor {
            font-size: 12px;
            color: #6c757d;
            margin: 0 0 15px 0;
        }

        .stats-table {
            width: 100%;
            border-spacing: 10px 0;
            border-collapse: separate;
            margin-bottom: 20px;
        }

        .stats-table td {
            width: 33.33%;
            text-align: center;
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 10px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #133b6a;
        }

        .stat-label {
            font-size: 9px;
            text-transform: uppercase;
        }

        .section-title {
            font-size: 14px;
            color: #133b6a;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .topics-list {
            list-style: none;
            padding-left: 0;
            margin-left: 0;
        }

        .topics-list li {
            padding: 6px 0;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
        }

        .students-table th {
            text-align: left;
            padding: 8px 5px;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-size: 9px;
            text-transform: uppercase;
        }

        .students-table td {
            padding: 8px 5px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }

        .students-table tbody tr:nth-child(even) td {
            background-color: #f8f9fa;
        }

        .students-table tbody tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <table class="header-content-table">
            <tr>
                <td class="header-logo-cell">
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo">
                </td>
                <td class="header-text-cell">
                    <h1>PEPS | HCAA</h1>
                    <p>Plataforma de Educação Permanente em Saúde</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Relatório gerado por {{ config('app.name') }} | Emitido em: {{ now()->format('d/m/Y H:i') }} | <span
            class="page-number"></span>
    </div>

    <main>
        <h1 class="document-title">Relatório de Aulas por Período</h1>
        <div class="report-period">
            <strong>Período Filtrado:</strong> de {{ $start_date->format('d/m/Y') }} a
            {{ $end_date->format('d/m/Y') }}
        </div>

        @forelse ($lessons as $lesson)
            <div class="lesson-card">
                <h2>{{ $lesson->name }}</h2>
                <p class="professor">Professor(a): {{ $lesson->teacher?->name ?? 'N/A' }}</p>

                <table class="stats-table">
                    <tr>
                        <td>
                            <div class="stat-value">{{ $lesson->total_subscriptions_count }}</div>
                            <div class="stat-label">Total de Alunos</div>
                        </td>
                        <td>
                            <div class="stat-value">{{ $lesson->subscriptions->count() }}</div>
                            <div class="stat-label">Concluíram no Período</div>
                        </td>
                        <td>
                            <div class="stat-value">{{ $lesson->workload }}h</div>
                            <div class="stat-label">Carga Horária</div>
                        </td>
                    </tr>
                </table>

                @if ($lesson->topics->isNotEmpty())
                    <h3 class="section-title">Tópicos</h3>
                    <ul class="topics-list">
                        @foreach ($lesson->topics as $topic)
                            <li>
                                <strong>{{ $topic->title }}:</strong>
                                <span style="color: #6c757d;">{{ strip_tags($topic->description) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <h3 class="section-title">Alunos Concluintes no Período</h3>
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>Estudante</th>
                            <th style="text-align: center;">Início</th>
                            <th style="text-align: center;">Conclusão</th>
                            <th style="text-align: center;">Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lesson->subscriptions as $student)
                            <tr>
                                <td>{{ $student->name }}<br><small style="color: #6c757d;">CPF:
                                        {{ $student->cpf }}</small></td>
                                <td style="text-align: center;">
                                    {{ optional($student->pivot->created_at)->format('d/m/Y') }}</td>
                                <td style="text-align: center;">
                                    {{ optional($student->pivot->finished_at)->format('d/m/Y') }}</td>
                                <td style="text-align: center;">{{ $student->pivot->score ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center;">Nenhum aluno concluinte para esta aula no
                                    período.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @empty
            <p style="text-align: center;">Nenhuma aula com concluintes encontrada para o período selecionado.</p>
        @endforelse
    </main>
</body>

</html>
