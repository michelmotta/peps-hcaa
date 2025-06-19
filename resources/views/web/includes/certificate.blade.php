<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            background-image: url('{{ public_path('images/certificate-bg.jpg') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .logo {
            position: absolute;
            top: 130px;
            left: 50%;
            transform: translateX(-50%);
            width: 150px;
        }

        .content-wrapper {
            padding: 160px 80px 60px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .title {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .content {
            font-size: 20pt;
            line-height: 1.5;
            margin-bottom: 60px;
        }

        .content strong {
            font-weight: bold;
        }

        .signature-table {
            width: 100%;
            margin-top: 60px;
            table-layout: fixed;
            padding: 0 100px;
        }

        .signature-table td {
            text-align: center;
            font-size: 14pt;
            padding-top: 20px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 20px;
            padding-top: 8px;
            display: inline-block;
            width: 250px;
        }
    </style>
</head>

<body>
    <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo">

    <div class="content-wrapper">
        <div class="title">Certificado de Conclusão</div>

        <div class="content">
            Certificamos que<br>
            <strong>{{ $user->name }}</strong><br>
            concluiu com êxito a aula<br>
            <strong>{{ $lesson->name }}</strong><br>
            em <strong>{{ $date }}</strong>.
        </div>

        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line">Coordenador</div>
                </td>
                <td>
                    <div class="signature-line">Instituição</div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
