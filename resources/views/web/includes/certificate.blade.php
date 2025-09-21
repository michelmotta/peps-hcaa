<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Certificado de Conclusão</title>
    <style>
        @page {
            margin: 0cm;
        }

        body {
            margin: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1D174F;
            background-color: #ffffff;
        }

        .background-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ public_path('images/certificate-pattern.jpg') }}');
            z-index: -1;
            opacity: 0.8;
        }

        .page-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .header-cell {
            background: #133b6a;
            color: white;
            padding: 30px 50px;
        }

        .header-cell h1 {
            margin: 0;
            font-size: 40px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .header-cell p {
            font-family: 'Times New Roman', Times, serif;
            font-size: 20px;
            margin: 5px 0 0 0;
            font-style: italic;
        }

        .yellow-bar-cell {
            height: 10px;
            background-color: #F5B800;
        }

        .content-cell {
            padding: 30px 50px 80px 50px;
            text-align: center;
            position: relative;
        }

        .presented {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .name {
            font-size: 38px;
            font-weight: bold;
            color: #2F276D;
            margin: 0;
        }

        .cpf-text {
            font-size: 14px;
            margin-top: 5px;
        }

        .description {
            margin-top: 20px;
            font-size: 16px;
            line-height: 1.6;
            max-width: 80%;
            margin-left: auto;
            margin-right: auto;
        }

        .signatures-table {
            width: 100%;
            margin-top: 100px;
        }

        .sig-cell {
            width: 50%;
            text-align: center;
        }

        .sig-line {
            border-bottom: 1px solid #333;
            width: 280px;
            margin: 0 auto 5px auto;
        }

        .sig-name {
            font-weight: bold;
            color: #2F276D;
        }

        .sig-role {
            font-size: 12px;
        }

        .validation-area {
            text-align: left;
            margin-top: 30px;
        }

        .qrcode {
            width: 80px;
            height: 80px;
        }

        .validation-text {
            font-size: 10px;
            color: #333;
            padding-left: 10px;
        }

        .validation-text p {
            margin: 4px 0;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
        }

        .footer-slogan {
            background: #133b6a;
            color: white;
            text-align: center;
            padding: 15px 50px;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .footer-yellow-bar {
            height: 10px;
            background-color: #F5B800;
        }

        /* Styles for the second page */
        .page-break {
            page-break-before: always;
        }

        .syllabus-page {
            padding: 40px 50px;
            text-align: left;
            color: #333;
        }

        .syllabus-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .syllabus-title {
            font-size: 24px;
            font-weight: bold;
            color: #133b6a;
            text-transform: uppercase;
            margin: 0;
        }

        .syllabus-subtitle {
            font-size: 18px;
            margin-top: 5px;
        }

        /* NEW: Table-based two-column layout */
        .syllabus-table {
            width: 100%;
            border-spacing: 20px;
            /* Creates space between columns */
            border-collapse: separate;
        }

        .syllabus-table-cell {
            width: 50%;
            vertical-align: top;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #133b6a;
            border-bottom: 2px solid #F5B800;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .professor-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .professor-expertise {
            font-size: 14px;
            font-style: italic;
            color: #555;
            margin-top: 2px;
        }

        .professor-description {
            font-size: 14px;
            line-height: 1.5;
            margin-top: 10px;
        }

        .topics-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .topics-list li {
            margin-bottom: 15px;
        }

        .topic-title {
            font-weight: bold;
            font-size: 15px;
        }

        .topic-resume {
            font-size: 14px;
            line-height: 1.5;
            margin-top: 5px;
            padding-left: 15px;
            border-left: 2px solid #e0e0e0;
        }
    </style>
</head>

<body>
    <div class="background-pattern"></div>
    <table class="page-table">
        <tbody>
            <tr>
                <td class="header-cell">
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <h1>Certificado de Conclusão</h1>
                                <p>Programa de Educação Permanente em Saúde</p>
                            </td>
                            <td style="text-align: right;">
                                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="width: 200px">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="yellow-bar-cell"></td>
            </tr>
            <tr>
                <td class="content-cell">
                    <p class="presented">O Programa de Educação Permanente em Saúde do Hospital de Câncer Alfredo Abrão
                        certifica, para os devidos fins, que</p>
                    <h2 class="name">{{ $user->name }}</h2>
                    <p class="cpf-text">portador(a) do CPF nº {{ $user->cpf }}</p>

                    <p class="description">
                        participou e concluiu com êxito a aula <strong>"{{ $lesson->name }}"</strong>,
                        @if ($lesson->professor)
                            ministrado pelo professor(a) {{ $lesson->professor->name }},
                        @endif
                        realizado no período de {{ $lessonUserData->created_at->format('d/m/Y') }} a
                        {{ $lessonUserData->finished_at->format('d/m/Y') }},
                        com carga horária total de {{ $lesson->workload }}.
                    </p>

                    <p class="description" style="margin-top: 25px">Campo Grande - MS, {{ $certificateDate }}</p>

                    <table class="signatures-table">
                        <tr>
                            <td class="sig-cell">
                                <div class="sig-line"></div>
                                <div class="sig-name">Nome do(a) Coordenador(a)</div>
                                <div class="sig-role">Coordenador(a) do PEPS</div>
                            </td>
                            <td class="sig-cell">
                                <div class="sig-line"></div>
                                <div class="sig-name">{{ $user->name }}</div>
                                <div class="sig-role">Estudante</div>
                            </td>
                        </tr>
                    </table>

                    <div class="validation-area">
                        <table style="border-spacing: 0;">
                            <tr>
                                <td style="padding: 0;">
                                    <img src="{{ $qrCodeBase64 }}" class="qrcode" alt="QR Code">
                                </td>
                                <td class="validation-text" style="vertical-align: middle;">
                                    <p>A autenticidade deste certificado pode ser confirmada no endereço:</p>
                                    <p><strong>{{ $validationUrl }}</strong></p>
                                    <p>Sob o código de verificação: <strong>{{ $validationCode }}</strong></p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-yellow-bar"></div>
        <div class="footer-slogan">
            Programa de Educação Permanente em Saúde<br>Hospital de Câncer Alfredo Abrão
        </div>
    </div>
    <div class="page-break"></div>
    <div class="syllabus-page">
        <div class="background-pattern"></div>
        <div class="syllabus-header">
            <h2 class="syllabus-title">Conteúdo Programático</h2>
            <p class="syllabus-subtitle">Aula: "{{ $lesson->name }}"</p>
        </div>

        <table class="syllabus-table">
            <tr>
                <td class="syllabus-table-cell">
                    @if ($lesson->teacher)
                        <div class="professor-section">
                            <h3 class="section-title">Professor(a) Responsável</h3>
                            <p class="professor-name">{{ $lesson->teacher->name }}</p>
                            @if ($lesson->teacher->expertise)
                                <p class="professor-expertise">{{ $lesson->teacher->expertise }}</p>
                            @endif
                            @if ($lesson->teacher->biography)
                                <p class="professor-description">{{ $lesson->teacher->biography }}</p>
                            @endif
                        </div>
                    @endif
                </td>
                <td class="syllabus-table-cell">
                    <div class="topics-section">
                        <h3 class="section-title">Tópicos Abordados</h3>
                        @if ($lesson->topics->isNotEmpty())
                            <ul class="topics-list">
                                @foreach ($lesson->topics as $topic)
                                    <li>
                                        <div class="topic-title">{{ $topic->title }}</div>
                                        @if ($topic->resume)
                                            <div class="topic-resume">{{ $topic->resume }}</div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p>Não há tópicos detalhados cadastrados para esta aula.</p>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
