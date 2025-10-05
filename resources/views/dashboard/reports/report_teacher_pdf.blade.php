<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Atividades do Professor</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            background-color: #FFFFFF;
        }

        /* --- Estrutura do PDF --- */
        .page-wrapper {
            padding: 30px;
        }

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

        .content {
            margin-top: 80px;
        }

        /* Aumentado para dar espaço ao header */
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

        /* --- Título e Período --- */
        .document-title {
            text-align: center;
            font-size: 20px;
            color: #333;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .report-period {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 12px;
            color: #6c757d;
        }

        /* --- Bloco de Informações do Professor --- */
        .teacher-info-container {
            padding-bottom: 20px;
            margin-bottom: 30px;
            border-bottom: 1px solid #e0e0e0;
            display: table;
            width: 100%;
        }

        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-column.right {
            padding-left: 20px;
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
            color: #333;
        }

        /* --- Tabela do PDF --- */
        .details-title {
            font-size: 18px;
            color: #133b6a;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th {
            text-align: left;
            padding: 10px 5px;
            border-bottom: 2px solid #133b6a;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
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

        .status-rascunho {
            background-color: #f0ad4e;
        }

        .status-aguardando {
            background-color: #5bc0de;
        }

        .status-publicada {
            background-color: #5cb85c;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-logo">
            {{-- Garanta que o caminho para a imagem esteja acessível pelo servidor --}}
            <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        </div>
        <div class="header-text">
            <h1 class="company-title">PEPS | HCAA</h1>
            <p>Plataforma de Educação Permanente em Saúde</p>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="content">

            <h1 class="document-title">Relatório de Aulas do Professor</h1>

            {{-- Seção para exibir o período do filtro --}}
            @if (request('start_date') || request('end_date'))
                <div class="report-period">
                    <strong>Período de Criação Filtrado:</strong>
                    @if (request('start_date'))
                        de {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}
                    @endif
                    @if (request('end_date'))
                        até {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                    @endif
                </div>
            @endif

            <div class="teacher-info-container">
                <div class="info-column left">
                    <h3>Informações do Professor</h3>
                    <ul class="info-list">
                        <li><span class="label">Nome:</span> <span class="value">{{ $teacher->name }}</span></li>
                        <li><span class="label">E-mail:</span> <span class="value">{{ $teacher->email }}</span></li>
                        <li><span class="label">CPF:</span> <span class="value">{{ $teacher->cpf }}</span></li>
                    </ul>
                </div>
                <div class="info-column right">
                    <h3>Resumo de Estatísticas</h3>
                    <ul class="info-list">
                        <li><span class="label">Total de Alunos:</span> <span
                                class="value">{{ $stats['total_students'] }}</span></li>
                        <li><span class="label">Total de Aulas Criadas:</span> <span
                                class="value">{{ $stats['created_lessons_count'] }}</span></li>
                        <li><span class="label">Aulas Publicadas:</span> <span
                                class="value">{{ $stats['status_counts'][\App\Enums\LessonStatusEnum::PUBLICADA->value] }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <h2 class="details-title">Histórico de Aulas</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 35%;">Aula</th>
                        <th style="width: 15%; text-align: center;">Criação</th>
                        <th style="width: 15%; text-align: center;">Status</th>
                        <th style="width: 10%; text-align: center;">Tópicos</th>
                        <th style="width: 10%; text-align: center;">C.H.</th>
                        <th style="width: 10%; text-align: center;">Alunos</th>
                        <th style="width: 10%; text-align: center;">Média</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lessons as $lesson)
                        <tr>
                            <td>
                                {{ $lesson->name }}<br>
                                <small style="color: #6c757d;">{{ $lesson->specialty->name ?? '' }}</small>
                            </td>
                            <td style="text-align: center;">{{ $lesson->created_at_formatted }}</td>
                            <td style="text-align: center;">
                                @if ($lesson->lesson_status === \App\Enums\LessonStatusEnum::RASCUNHO->value)
                                    <span class="badge status-rascunho">Rascunho</span>
                                @elseif ($lesson->lesson_status === \App\Enums\LessonStatusEnum::AGUARDANDO_PUBLICACAO->value)
                                    <span class="badge status-aguardando">Aguardando</span>
                                @elseif ($lesson->lesson_status === \App\Enums\LessonStatusEnum::PUBLICADA->value)
                                    <span class="badge status-publicada">Publicada</span>
                                @endif
                            </td>
                            <td style="text-align: center;">{{ $lesson->topics->count() }}</td>
                            <td style="text-align: center;">{{ $lesson->workload }}h</td>
                            <td style="text-align: center;">{{ $lesson->subscriptions_count }}</td>
                            <td style="text-align: center;">
                                {{ $lesson->average_score ? number_format($lesson->average_score, 1) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px;">Nenhuma aula encontrada para
                                os filtros aplicados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        Relatório gerado por {{ config('app.name') }} | Emitido em: {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
