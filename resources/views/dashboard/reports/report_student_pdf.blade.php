<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Atividades do Estudante</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; margin: 0; background-color: #FFFFFF; }

        /* --- PDF Structure --- */
        .page-wrapper { padding: 30px; }
        .header { background-color: #133b6a; color: #FFFFFF; padding: 20px 30px; display: table; width: 100%; position: absolute; top: 0; left: 0; right: 0;}
        .header-logo { display: table-cell; width: 80px; vertical-align: middle; }
        .header-logo img { max-height: 40px; }
        .header-text { display: table-cell; vertical-align: middle; padding-left: 20px; }
        .header-text h1 { font-size: 18px; font-weight: bold; margin: 0; }
        .header-text p { font-size: 13px; margin: 4px 0 0 0; opacity: 0.9; }
        .content { margin-top: 80px; }
        .footer { position: fixed; bottom: 0; left: 30px; right: 30px; text-align: center; font-size: 10px; color: #999; padding: 15px 0; border-top: 1px solid #e0e0e0; }

        /* --- Document Title & Period --- */
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

        /* --- Student Info Block --- */
        .student-info-container {
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
        
        /* --- PDF Table --- */
        .details-title { font-size: 18px; color: #133b6a; margin-bottom: 15px; font-weight: bold; text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px 5px; border-bottom: 2px solid #133b6a; font-size: 9px; text-transform: uppercase; font-weight: bold; }
        td { padding: 10px 5px; border-bottom: 1px solid #e0e0e0; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        .badge { padding: 4px 8px; border-radius: 4px; font-weight: bold; }
        .status-completed { background-color: #e7f0fa; color: #133b6a; }
        .status-inprogress { background-color: #FEF9E7; color: #B7950B; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo">
        </div>
        <div class="header-text">
            <h1 class="company-title">PEPS | HCAA</h1>
            <p>Programa de Educação Permanente em Saúde</p>
        </div>
    </div>

    <div class="page-wrapper">
        <div class="content">

            <h1 class="document-title">Relatório de Aulas do Estudante</h1>

            {{-- New Section to Display the Filter Period --}}
            @if(request('start_date') || request('end_date'))
                <div class="report-period">
                    <strong>Período de Início Filtrado:</strong> 
                    @if(request('start_date'))
                        de {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }}
                    @endif
                    @if(request('end_date'))
                        até {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                    @endif
                </div>
            @endif
            
            <div class="student-info-container">
                <div class="info-column left">
                    <h3>Informações do Estudante</h3>
                    <ul class="info-list">
                        <li><span class="label">Nome:</span> <span class="value">{{ $student->name }}</span></li>
                        <li><span class="label">Email:</span> <span class="value">{{ $student->email }}</span></li>
                        <li><span class="label">CPF:</span> <span class="value">{{ $student->cpf ?? '-' }}</span></li>
                    </ul>
                </div>
                <div class="info-column right">
                    <h3>Resumo de Atividades</h3>
                    <ul class="info-list">
                        <li><span class="label">Aulas Concluídas:</span> <span class="value">{{ $student->completed_subscriptions_count }} / {{ $student->subscriptions_count }}</span></li>
                        <li><span class="label">Carga Horária Cumprida:</span> <span class="value">{{ $completedWorkload ?? 0 }} horas</span></li>
                        <li><span class="label">Último Login:</span> <span class="value">{{ $student->lastLogin?->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</span></li>
                    </ul>
                </div>
            </div>

            <h2 class="details-title">Histórico de Aulas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Aula</th>
                        <th style="text-align: center;">Carga Horária</th>
                        <th style="text-align: center;">Início</th>
                        <th style="text-align: center;">Conclusão</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subscriptions as $subscription)
                        <tr>
                            <td>
                                {{ $subscription->name }}<br>
                                <small style="color: #6c757d;">{{ $subscription->teacher->name ?? '' }}</small>
                            </td>
                            <td style="text-align: center;">{{ $subscription->workload }}h</td>
                            <td style="text-align: center;">{{ optional($subscription->pivot->created_at)->format('d/m/Y') ?? '-' }}</td>
                            <td style="text-align: center;">{{ optional($subscription->pivot->finished_at)->format('d/m/Y') ?? '-' }}</td>
                            <td style="text-align: center;">
                                @if ($subscription->pivot->finished)
                                    <span class="badge status-completed">Concluído</span>
                                @else
                                    <span class="badge status-inprogress">Em Andamento</span>
                                @endif
                            </td>
                            <td style="text-align: center;">{{ $subscription->pivot->score ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">Nenhuma atividade encontrada para os filtros aplicados.</td>
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