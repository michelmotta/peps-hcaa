<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quizzes = [
            [
                'topic_id' => 1,
                'question' => 'Qual é o valor mínimo considerado como hipertensão arterial em adultos?',
                'options' => [
                    ['A' => '120/80 mmHg'],
                    ['B' => '130/85 mmHg'],
                    ['C' => '140/90 mmHg'],
                    ['D' => '150/95 mmHg']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 1,
                'question' => 'Qual método é indicado para confirmar o diagnóstico de hipertensão arterial?',
                'options' => [
                    ['A' => 'Pressão casual em consultório'],
                    ['B' => 'MAPA (Monitorização Ambulatorial da Pressão Arterial)'],
                    ['C' => 'Exame de sangue'],
                    ['D' => 'Eletrocardiograma']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 1,
                'question' => 'Qual fator de risco está mais relacionado ao desenvolvimento de hipertensão arterial?',
                'options' => [
                    ['A' => 'Baixo consumo de sal'],
                    ['B' => 'Sedentarismo'],
                    ['C' => 'Boa qualidade do sono'],
                    ['D' => 'Hidratação constante']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 2,
                'question' => 'Qual classe de medicamento é geralmente a primeira escolha no tratamento da hipertensão?',
                'options' => [
                    ['A' => 'Antibióticos'],
                    ['B' => 'Diuréticos tiazídicos'],
                    ['C' => 'Estatinas'],
                    ['D' => 'Anti-inflamatórios']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 2,
                'question' => 'Qual efeito colateral é comum com o uso de IECA (Inibidores da Enzima Conversora de Angiotensina)?',
                'options' => [
                    ['A' => 'Tosse seca'],
                    ['B' => 'Sonolência'],
                    ['C' => 'Hiperglicemia'],
                    ['D' => 'Diarreia']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 3,
                'question' => 'Qual sintoma é mais característico da insuficiência cardíaca?',
                'options' => [
                    ['A' => 'Febre alta'],
                    ['B' => 'Falta de ar aos esforços'],
                    ['C' => 'Dor de garganta'],
                    ['D' => 'Vômitos persistentes']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 3,
                'question' => 'A congestão pulmonar na IC está relacionada com falência de qual câmara cardíaca?',
                'options' => [
                    ['A' => 'Átrio direito'],
                    ['B' => 'Ventrículo esquerdo'],
                    ['C' => 'Átrio esquerdo'],
                    ['D' => 'Ventrículo direito']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 4,
                'question' => 'Qual classe de fármaco melhora a sobrevida em pacientes com IC?',
                'options' => [
                    ['A' => 'Diuréticos'],
                    ['B' => 'Digitálicos'],
                    ['C' => 'Betabloqueadores'],
                    ['D' => 'Anti-histamínicos']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),

            ],
            [
                'topic_id' => 5,
                'question' => 'Qual marcador laboratorial é mais sensível para o diagnóstico de infarto agudo do miocárdio?',
                'options' => [
                    ['A' => 'Troponina'],
                    ['B' => 'CPK total'],
                    ['C' => 'Mioglobina'],
                    ['D' => 'D-dímero']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 5,
                'question' => 'O que caracteriza uma SCA com supradesnivelamento do segmento ST?',
                'options' => [
                    ['A' => 'Isquemia silente'],
                    ['B' => 'Infarto transmural'],
                    ['C' => 'Angina estável'],
                    ['D' => 'Embolia pulmonar']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 6,
                'question' => 'Qual medicamento é indicado como antiagregante plaquetário na SCA?',
                'options' => [
                    ['A' => 'Paracetamol'],
                    ['B' => 'Clopidogrel'],
                    ['C' => 'Losartana'],
                    ['D' => 'Omeprazol']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 6,
                'question' => 'Qual o tempo ideal para realização da angioplastia primária após o início dos sintomas?',
                'options' => [
                    ['A' => 'Até 1 hora'],
                    ['B' => 'Até 6 horas'],
                    ['C' => 'Até 12 horas'],
                    ['D' => 'Até 24 horas']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 7,
                'question' => 'Qual exame é fundamental para o diagnóstico de arritmias cardíacas?',
                'options' => [
                    ['A' => 'Raio-x de tórax'],
                    ['B' => 'Tomografia de tórax'],
                    ['C' => 'Eletrocardiograma (ECG)'],
                    ['D' => 'Exame de urina']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 7,
                'question' => 'Qual complicação é mais comum na fibrilação atrial não tratada?',
                'options' => [
                    ['A' => 'Infecção respiratória'],
                    ['B' => 'Acidente vascular cerebral (AVC)'],
                    ['C' => 'Insônia'],
                    ['D' => 'Hipoglicemia']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 8,
                'question' => 'Qual tratamento pode ser usado para reverter a fibrilação atrial?',
                'options' => [
                    ['A' => 'Cardioversão elétrica'],
                    ['B' => 'Hemodiálise'],
                    ['C' => 'Fisioterapia'],
                    ['D' => 'Transfusão de sangue']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'topic_id' => 8,
                'question' => 'Qual classe de medicamentos é usada para controlar a frequência cardíaca nas arritmias?',
                'options' => [
                    ['A' => 'Antibióticos'],
                    ['B' => 'Betabloqueadores'],
                    ['C' => 'Laxantes'],
                    ['D' => 'Antieméticos']
                ],
                'correct' => 'A',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        foreach ($quizzes as $quiz) {
            Quiz::create($quiz);
        }
    }
}
