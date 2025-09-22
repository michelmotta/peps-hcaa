<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Aulas por Período</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
        }

        /* --- PDF Structure --- */
        .header {
            background-color: #133b6a;
            color: #FFFFFF;
            padding: 20px 30px;
            display: table;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
        }

        .header-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
        }

        .header-logo img {
            max-height: 40px;
        }

        .header-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 20px;
        }

        .header-text h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        .header-text p {
            font-size: 13px;
            margin: 4px 0 0 0;
            opacity: 0.9;
        }

        .page-wrapper {
            padding: 30px;
        }

        .content {
            margin-top: 100px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            padding: 15px 0;
            border-top: 1px solid #e0e0e0;
        }

        /* --- General Typography --- */
        .document-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .report-period {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            font-size: 12px;
            color: #6c757d;
        }

        .lesson-block {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .lesson-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .lesson-header h2 {
            font-size: 18px;
            color: #133b6a;
            margin: 0;
        }

        .lesson-body {
            padding: 15px;
        }

        .section-title {
            font-size: 14px;
            color: #133b6a;
            margin-top: 0;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .topics-list {
            list-style: none;
            padding-left: 0;
            margin-left: 0;
            margin-bottom: 15px;
        }

        .topics-list li {
            padding-bottom: 8px;
            padding-top: 8px;
            border-bottom: 1px dotted #ccc;
        }

        .topics-list li:last-child {
            border-bottom: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 8px 5px;
            border-bottom: 2px solid #133b6a;
            font-size: 9px;
            text-transform: uppercase;
        }

        td {
            padding: 8px 5px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        </div>
        <div class="header-text">
            <h1>PEPS | HCAA</h1>
            <p>Programa de Educação Permanente em Saúde</p>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="content">
            <h1 class="document-title">Relatório de Aulas por Período</h1>
            <div class="report-period">
                <strong>Período Filtrado:</strong> de {{ $start_date->format('d/m/Y') }} a
                {{ $end_date->format('d/m/Y') }}
            </div>

            @forelse ($lessons as $lesson)
                <div class="lesson-block">
                    <div class="lesson-header">
                        <h2>{{ $lesson->name }}</h2>
                    </div>
                    <div class="lesson-body">
                        <p>
                            <strong>Professor(a):</strong> {{ $lesson->teacher?->name ?? 'N/A' }} |
                            <strong>Total de Alunos:</strong> {{ $lesson->total_subscriptions_count }} |
                            <strong>Concluíram no Período:</strong> {{ $lesson->subscriptions->count() }}
                        </p>

                        <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 15px 0;">

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
                        <table>
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
                                        <td>{{ $student->name }}<br><small
                                                style="color: #6c757d;">{{ $student->email }}</small></td>
                                        <td style="text-align: center;">
                                            {{ optional($student->pivot->created_at)->format('d/m/Y') }}</td>
                                        <td style="text-align: center;">
                                            {{ optional($student->pivot->finished_at)->format('d/m/Y') }}</td>
                                        <td style="text-align: center;">{{ $student->pivot->score ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" style="text-align: center;">Nenhum aluno concluinte para esta
                                            aula no período.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <p style="text-align: center;">Nenhuma aula com concluintes encontrada para o período selecionado.</p>
            @endforelse
        </div>
    </div>

    <div class="footer">
        Relatório gerado por {{ config('app.name') }} | Emitido em: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
