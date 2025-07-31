<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <title>{{ $comunicado->subject }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', Arial, sans-serif; background-color: #f7f9fb;">
    <div style="display: none; max-height: 0; overflow: hidden;">
        Novo comunicado na aula: {{ $lesson->name }}
    </div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f7f9fb;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="100%" style="max-width: 600px;" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <td align="center" style="padding: 25px; background-color: #133b6a; background: linear-gradient(160deg, #1a4c8a 0%, #133b6a 100%);">
                                        <img src="{{ $message->embed(public_path('images/logo-home.png')) }}" alt="Logo PEPS" width="150" style="display: block;">
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 40px;">
                                        <h1 style="font-size: 24px; color: #133b6a; margin-top: 0; font-weight: 700; text-align: center;">
                                            Novo Comunicado
                                        </h1>
                                        <p style="color: #4a5568; line-height: 1.7; font-size: 16px; text-align: left;">
                                            Olá, {{ $student->name }},
                                        </p>
                                        <p style="color: #4a5568; line-height: 1.7; font-size: 16px; text-align: left;">
                                            Um comunicado importante foi postado na aula <strong>{{ $lesson->name }}</strong>. Veja os detalhes abaixo:
                                        </p>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 25px 0; background-color: #f7f9fb; border-radius: 8px; border-left: 5px solid #1a4c8a;">
                                            <tr>
                                                <td style="padding: 20px;">
                                                    <h2 style="font-size: 18px; color: #1a202c; margin: 0 0 10px 0; font-weight: 600;">{{ $comunicado->subject }}</h2>
                                                    <div style="color: #4a5568; line-height: 1.7; font-size: 16px;">
                                                        {!! $comunicado->description !!}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <table border="0" cellspacing="0" cellpadding="0" width="100%" style="margin-top: 10px;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ route('web.class', $lesson->id) }}"
                                                       style="display: inline-block; padding: 14px 28px; background-color: #133b6a; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;">
                                                        Acessar Aula
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 30px 20px; color: #718096; font-size: 12px;">
                            <p style="margin: 0;">&copy; {{ date('Y') }} {{ config('app.name') }}.</p>
                            <p style="margin: 5px 0 0 0;">Hospital de Câncer Alfredo Abrão.</p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>
