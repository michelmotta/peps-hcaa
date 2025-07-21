<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @page {
            margin: 0cm;
        }

        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
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
            background-repeat: repeat;
            z-index: -1;
            opacity: 0.5;
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
            font-family: cursive, sans-serif;
            font-size: 20px;
            margin: 0;
        }

        .logo {
            width: 70px;
            vertical-align: middle;
        }

        .yellow-bar-cell {
            height: 10px;
            background-color: #F5B800;
            font-size: 0;
            line-height: 0;
        }

        .content-cell {
            padding: 40px 50px 100px 50px;
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
            margin-top: 15px;
            font-size: 16px;
            line-height: 1.5;
        }

        .signatures-table {
            width: 100%;
            margin-top: 65px;
        }

        .sig-cell {
            width: 50%;
            text-align: center;
        }

        .sig-line {
            border-bottom: 1px solid #333;
            width: 250px;
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
            margin-top: 40px;
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
                                <h1>Certificado</h1>
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
                    <p class="presented">O Programa de Educação Permanente em Saúde do Hopsital de Câncer de Campo Grande <br>certifica, para os devidos
                        fins, que</p>
                    <h2 class="name">{{ $user->name }}</h2>
                    <p class="cpf-text">portador(a) do CPF nº {{ $user->cpf }}</p>
                    <p class="description">atuou na qualidade de professor(a) e idealizador(a) do curso
                        <strong>"{{ $lesson->name }}"</strong>,
                        com carga horária de {{ $lesson->workload }} horas, e reconhece sua valiosa contribuição para o
                        avanço educacional
                        desta instituição.
                    </p>
                    <p class="description" style="margin-top: 30px">Campo Grande - MS, {{ $certificateDate }}</p>
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
                                <div class="sig-role">Professor</div>
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
                                    <p>A autenticidade deste certificado pode ser confirmado no endereço:</p>
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
            PEPS - Programa de Educação Permanente em Saúde<br>Hospital de Câncer Alfredo Abrão
        </div>
    </div>
</body>

</html>