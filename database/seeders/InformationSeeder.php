<?php

namespace Database\Seeders;

use App\Models\Information;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $informations = [
            [
                'title' => 'Como me inscrevo nas aulas online?',
                'description' => 'Você pode se inscrever criando uma conta gratuita em nossa plataforma e escolhendo o curso desejado na área de cursos disponíveis.',
                'published' => true,
                'user_id' => 1
            ],
            [
                'title' => 'Preciso pagar para acessar os cursos?',
                'description' => 'Oferecemos cursos gratuitos e pagos. Cada curso informa claramente se há algum custo associado.',
                'published' => true,
                'user_id' => 1
            ],
            [
                'title' => 'As aulas ficam gravadas?',
                'description' => 'Sim! Todas as aulas ficam gravadas e disponíveis para você assistir quando quiser, no seu ritmo.',
                'published' => true,
                'user_id' => 1
            ],
            [
                'title' => 'Consigo emitir certificado de conclusão?',
                'description' => 'Sim! Após concluir todos os módulos e avaliações de um curso, você poderá gerar seu certificado digital diretamente pela plataforma.',
                'published' => true,
                'user_id' => 1
            ],
            [
                'title' => 'Posso acessar a plataforma pelo celular?',
                'description' => 'Claro! Nossa plataforma é totalmente responsiva e também possui aplicativo para iOS e Android.',
                'published' => true,
                'user_id' => 1
            ],
        ];


        foreach ($informations as $information) {
            Information::create($information);
        }
    }
}
