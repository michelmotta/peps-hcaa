<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório da Aula: {{ $lesson->name }}</title>
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

        .page-wrapper {
            padding: 30px;
            padding-top: 100px; /* Increased space for the header */
        }

        /* --- ✅ Corrected Header & Footer --- */
        .header {
            background-color: #133b6a;
            color: #FFFFFF;
            padding: 20px 30px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            /* Using table layout to prevent overlap */
            display: table;
            width: 100%;
        }

        .header-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
        }

        .header-logo img {
            max-height: 40px;
            width: auto;
        }

        .header-text {
            display: table-cell;
            vertical-align: middle;
            padding-left: 20px;
        }

        .header-text h1 {
            font-size: 18px;
            margin: 0;
        }

        .header-text p {
            font-size: 13px;
            margin: 4px 0 0 0;
            opacity: 0.9;
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
        
        /* --- General Layout --- */
        .document-title {
            text-align: center;
            font-size: 20px;
            color: #333;
            margin-bottom: 25px;
            font-weight: bold;
        }
        
        .info-container {
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
            width: 100%;
        }

        .info-container::after {
            content: "";
            display: table;
            clear: both;
        }

        .info-column {
            float: left;
            width: 48%;
        }

        .info-column.right {
            margin-left: 4%;
        }

        .info-column h3 {
            font-size: 14px;
            color: #133b6a;
            margin-top: 0;
            margin-bottom: 10px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 12px;
        }

        .info-list li {
            padding: 4px 0;
        }

        .info-list .label {
            color: #6c757d;
        }

        .info-list .value {
            font-weight: bold;
        }

        .section-title {
            font-size: 16px;
            color: #133b6a;
            margin-bottom: 15px;
            font-weight: bold;
            /* ✅ Centered title */
            text-align: center;
        }

        /* --- Table Styling --- */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 25px;
        }

        th {
            text-align: left;
            padding: 10px 5px;
            border-bottom: 2px solid #133b6a;
            font-size: 9px;
            text-transform: uppercase;
        }

        td {
            padding: 8px 5px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
            word-wrap: break-word;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            color: #fff;
        }

        .status-concluido { background-color: #5cb85c; }
        .status-andamento { background-color: #f0ad4e; }
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

    <div class="footer">
        Relatório gerado por {{ config('app.name') }} | Emitido em: {{ now()->format('d/m/Y H:i') }}
    </div>

    <main class="page-wrapper">
        <h1 class="document-title">Relatório da Aula</h1>

        <div class="info-container">
            <div class="info-column left">
                <h3>Informações da Aula</h3>
                <ul class="info-list">
                    <li><span class="label">Aula:</span> <span class="value">{{ $lesson->name }}</span></li>
                    <li><span class="label">Professor:</span> <span class="value">{{ $lesson->teacher?->name ?? 'N/A' }}</span></li>
                    <li><span class="label">Especialidade:</span> <span class="value">{{ $lesson->specialty?->name ?? 'N/A' }}</span></li>
                    <li><span class="label">Carga Horária:</span> <span class="value">{{ $lesson->workload }}h</span></li>
                </ul>
            </div>
            <div class="info-column right">
                <h3>Resumo de Estatísticas</h3>
                <ul class="info-list">
                    <li><span class="label">Status:</span> <span class="value">{{ \App\Enums\LessonStatusEnum::getLessonStatusNameById($lesson->lesson_status) }}</span></li>
                    <li><span class="label">Total de Alunos:</span> <span class="value">{{ $lesson->subscriptions_count }}</span></li>
                    <li><span class="label">Alunos que Concluíram:</span> <span class="value">{{ $lesson->completed_subscriptions_count }}</span></li>
                    <li><span class="label">Nota Média:</span> <span class="value">{{ number_format($lesson->average_score, 1) }}</span></li>
                </ul>
            </div>
        </div>

        <h2 class="section-title">Tópicos da Aula</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 30%;">Título</th>
                    <th style="width: 70%;">Descrição</th>
                </tr>
            </thead>
            <tbody>
                 @forelse ($lesson->topics as $topic)
                    <tr>
                        <td>{{ $topic->title }}</td>
                        <td>{{ $topic->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" style="text-align: center; padding: 20px;">Nenhum tópico cadastrado para esta aula.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <h2 class="section-title">Alunos Inscritos na Aula</h2>
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Estudante</th>
                    <th style="width: 15%; text-align: center;">Início</th>
                    <th style="width: 15%; text-align: center;">Conclusão</th>
                    <th style="width: 20%; text-align: center;">Status</th>
                    <th style="width: 15%; text-align: center;">Nota</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    <tr>
                        <td>
                            {{ $student->name }}<br>
                            <small style="color: #6c757d;">{{ $student->email }}</small>
                        </td>
                        <td style="text-align: center;">{{ $student->pivot->created_at->format('d/m/Y') }}</td>
                        <td style="text-align: center;">
                            {{ $student->pivot->finished_at ? \Carbon\Carbon::parse($student->pivot->finished_at)->format('d/m/Y') : '-' }}
                        </td>
                        <td style="text-align: center;">
                            @if ($student->pivot->finished)
                                <span class="badge status-concluido">Concluído</span>
                            @else
                                <span class="badge status-andamento">Em Andamento</span>
                            @endif
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ $student->pivot->score ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">
                            Nenhum aluno inscrito nesta aula.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>