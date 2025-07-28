@extends('templates.web')

@section('content')
    <section>
        <div class="content-title">
            <h1>Termos de Uso</h1>
            <p class="sub-title">Conheça os termos de uso da plataforma</p>
        </div>
    </section>

    <section class="termos-uso px-4 py-5">
        <div class="container">
            <h2>1. Aceitação dos Termos</h2>
            <p>Ao acessar e utilizar esta plataforma, você concorda em cumprir os termos e condições aqui estabelecidos.
                Caso não concorde com algum destes termos, por favor, não utilize a plataforma.</p>

            <h2>2. Uso da Plataforma</h2>
            <p>Você concorda em utilizar a plataforma apenas para fins legais e que não violem direitos de terceiros. É
                proibido o uso da plataforma para práticas ilegais, difamatórias, ofensivas ou que causem danos a outros
                usuários ou à plataforma.</p>

            <h2>3. Propriedade Intelectual</h2>
            <p>Todo o conteúdo disponível na plataforma, incluindo textos, imagens, logotipos, vídeos e código-fonte, é
                protegido por direitos autorais e pertence à empresa ou aos seus licenciadores.</p>

            <h2>4. Cadastro e Segurança</h2>
            <p>Alguns recursos podem exigir cadastro. Você é responsável por manter suas credenciais de acesso em segurança
                e por todas as atividades realizadas com sua conta.</p>

            <h2>5. Modificações nos Termos</h2>
            <p>Reservamo-nos o direito de alterar estes Termos de Uso a qualquer momento. Recomendamos que você os revise
                periodicamente para estar ciente de eventuais mudanças.</p>

            <h2>6. Limitação de Responsabilidade</h2>
            <p>Não nos responsabilizamos por danos diretos, indiretos ou incidentais resultantes do uso ou da incapacidade
                de uso da plataforma.</p>

            <h2>7. Contato</h2>
            <p>Em caso de dúvidas sobre estes Termos de Uso, entre em contato conosco através dos canais disponíveis na
                plataforma.</p>

            <p class="mt-4"><strong>Data da última atualização:</strong> {{ now()->format('d/m/Y') }}</p>
        </div>
    </section>
@endsection
