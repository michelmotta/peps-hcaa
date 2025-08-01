<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <title>Bem-vindo(a) ao PEPS!</title>
</head>

<body style="margin: 0; padding: 0; font-family: 'Inter', Arial, sans-serif; background-color: #f7f9fb;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f7f9fb;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table width="100%" style="max-width: 600px;" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td
                            style="background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center"
                                        style="padding: 25px; background-color: #133b6a; background: linear-gradient(160deg, #1a4c8a 0%, #133b6a 100%);">
                                        <img src="{{ $message->embed(public_path('images/logo-home.png')) }}"
                                            alt="Logo PEPS" width="150" style="display: block;">
                                    </td>
                                </tr>
                            </table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="padding: 40px;">
                                        <h1
                                            style="font-size: 24px; color: #133b6a; margin-top: 0; font-weight: 700; text-align: center;">
                                            Sua Conta Foi Criada!
                                        </h1>
                                        <p style="color: #4a5568; line-height: 1.7; font-size: 16px; text-align: left;">
                                            Olá, {{ $user->name }},
                                        </p>
                                        <p style="color: #4a5568; line-height: 1.7; font-size: 16px; text-align: left;">
                                            Seja muito bem-vindo(a) ao <strong>PEPS - Programa de Educação Permanente em
                                                Saúde</strong>! Estamos felizes em ter você conosco.
                                        </p>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                            style="margin: 20px 0; background-color: #fffbe6; border-radius: 8px; border-left: 5px solid #f5b800;">
                                            <tr>
                                                <td style="padding: 20px;">
                                                    <h2
                                                        style="font-size: 16px; color: #856404; margin: 0 0 5px 0; font-weight: 600;">
                                                        Atenção: Próximo Passo</h2>
                                                    <p
                                                        style="color: #856404; line-height: 1.6; font-size: 15px; margin: 0;">
                                                        Para acessar a plataforma, um coordenador precisa autorizar seu
                                                        cadastro. Caso a liberação demore, por favor, entre em contato
                                                        com a coordenação.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                        <table border="0" cellspacing="0" cellpadding="0" width="100%"
                                            style="margin-top: 20px; margin-bottom: 20px;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ route('login') }}"
                                                        style="display: inline-block; padding: 14px 28px; background-color: #133b6a; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;">
                                                        Ir para a Página de Login
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
                            <p style="margin: 5px 0 0 0;">Hospital de Câncer Alfredo Abrão.
                            </p>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>

</html>
