<?php

namespace Database\Seeders;

use App\Models\Topic;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topicos = [
            // Lesson 1
            [
                'title' => 'Hipertensão arterial: diagnóstico e abordagem inicial',
                'resume' => 'Compreenda os principais métodos de diagnóstico da hipertensão arterial e primeiros passos no manejo.',
                'description' => 'Nesta aula, abordaremos os critérios diagnósticos atualizados para hipertensão arterial, incluindo a importância da aferição correta da pressão arterial, além de discutirmos os fatores de risco associados.',
                'lesson_id' => 1,
                'video_id' => 1,
                'attachments' => [
                    [
                        'name' => 'pdf_sample.pdf',
                        'path' => 'uploads/lessons/attachments/rWoewuRBFx6yUqySNWFAt7m3zNS3YnJc7yorMUNx.pdf',
                        'size' => 10527,
                        'extension' => 'pdf',
                        'date' => Carbon::now()->format('d/m/Y')
                    ]
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Hipertensão arterial: tratamento farmacológico',
                'resume' => 'Conheça as principais classes de medicamentos usados no tratamento da hipertensão arterial.',
                'description' => 'Vamos explorar os diferentes grupos de fármacos indicados para o controle da pressão arterial, como diuréticos, bloqueadores dos canais de cálcio e inibidores da ECA, incluindo quando e como utilizá-los.',
                'lesson_id' => 1,
                'video_id' => 2,
                'attachments' => [
                    [
                        'name' => 'pdf_sample.pdf',
                        'path' => 'uploads/lessons/attachments/rWoewuRBFx6yUqySNWFAt7m3zNS3YnJc7yorMUNx.pdf',
                        'size' => 10527,
                        'extension' => 'pdf',
                        'date' => Carbon::now()->format('d/m/Y')
                    ]
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Lesson 2
            [
                'title' => 'Insuficiência cardíaca: fisiopatologia e sintomas',
                'resume' => 'Entenda os mecanismos fisiopatológicos da insuficiência cardíaca e seus principais sintomas clínicos.',
                'description' => 'A aula trata dos tipos de insuficiência cardíaca, suas causas principais, alterações hemodinâmicas envolvidas e como esses fatores resultam nos sintomas clássicos da condição.',
                'lesson_id' => 2,
                'video_id' => 3,
                'attachments' => [
                    [
                        'name' => 'pdf_sample.pdf',
                        'path' => 'uploads/lessons/attachments/rWoewuRBFx6yUqySNWFAt7m3zNS3YnJc7yorMUNx.pdf',
                        'size' => 10527,
                        'extension' => 'pdf',
                        'date' => Carbon::now()->format('d/m/Y')
                    ]
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Insuficiência cardíaca: tratamento e prognóstico',
                'resume' => 'Abordagem terapêutica da insuficiência cardíaca e fatores que influenciam o prognóstico.',
                'description' => 'Nesta sessão, discutiremos os objetivos terapêuticos, medicamentos indicados, papel dos dispositivos eletrônicos e critérios para transplante cardíaco em pacientes com IC.',
                'lesson_id' => 2,
                'video_id' => 1,
                'attachments' => [
                    [
                        'name' => 'pdf_sample.pdf',
                        'path' => 'uploads/lessons/attachments/rWoewuRBFx6yUqySNWFAt7m3zNS3YnJc7yorMUNx.pdf',
                        'size' => 10527,
                        'extension' => 'pdf',
                        'date' => Carbon::now()->format('d/m/Y')
                    ]
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Lesson 3
            [
                'title' => 'Síndrome coronariana aguda: fisiopatologia e diagnóstico',
                'resume' => 'Explore os mecanismos que levam à síndrome coronariana aguda e como realizar um diagnóstico eficaz.',
                'description' => 'Nesta aula, explicaremos a fisiopatologia da síndrome coronariana aguda (SCA), com foco na ruptura da placa aterosclerótica, além dos critérios diagnósticos baseados em eletrocardiograma, marcadores de necrose e quadro clínico.',
                'lesson_id' => 3,
                'video_id' => 2,
                'attachments' => [
                    [
                        'name' => 'pdf_sample.pdf',
                        'path' => 'uploads/lessons/attachments/rWoewuRBFx6yUqySNWFAt7m3zNS3YnJc7yorMUNx.pdf',
                        'size' => 10527,
                        'extension' => 'pdf',
                        'date' => Carbon::now()->format('d/m/Y')
                    ]
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Síndrome coronariana aguda: condutas e tratamento',
                'resume' => 'Veja as condutas imediatas e o tratamento farmacológico e intervencionista da SCA.',
                'description' => 'A aula aborda o tratamento inicial da SCA com e sem supradesnivelamento do ST, incluindo o uso de antiagregantes plaquetários, anticoagulantes, trombolíticos e estratégias de reperfusão coronariana como angioplastia.',
                'lesson_id' => 3,
                'video_id' => 3,
                'attachments' => [
                    [
                        'name' => 'pdf_sample.pdf',
                        'path' => 'uploads/lessons/attachments/rWoewuRBFx6yUqySNWFAt7m3zNS3YnJc7yorMUNx.pdf',
                        'size' => 10527,
                        'extension' => 'pdf',
                        'date' => Carbon::now()->format('d/m/Y')
                    ]
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // Lesson 4
            [
                'title' => 'Arritmias cardíacas: diagnóstico e implicações clínicas',
                'resume' => 'Aprenda como reconhecer os principais tipos de arritmias cardíacas e seus impactos no paciente.',
                'description' => 'Abordaremos os tipos mais comuns de arritmias como fibrilação atrial, taquicardias supraventriculares e bradicardias, incluindo o diagnóstico por ECG e os riscos de eventos embólicos ou síncopes.',
                'lesson_id' => 4,
                'video_id' => 1,
                'attachments' => [
                    [
                        'name' => 'pdf_sample.pdf',
                        'path' => 'uploads/lessons/attachments/rWoewuRBFx6yUqySNWFAt7m3zNS3YnJc7yorMUNx.pdf',
                        'size' => 10527,
                        'extension' => 'pdf',
                        'date' => Carbon::now()->format('d/m/Y')
                    ]
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'title' => 'Arritmias cardíacas: tratamento farmacológico e não farmacológico',
                'resume' => 'Conheça os principais medicamentos e procedimentos utilizados no tratamento das arritmias.',
                'description' => 'Nesta aula final, discutiremos o uso de antiarrítmicos, betabloqueadores e anticoagulantes, além de abordagens como cardioversão elétrica, ablação por cateter e uso de marcapasso.',
                'lesson_id' => 4,
                'video_id' => 2,
                'attachments' => [
                    [
                        'name' => 'pdf_sample.pdf',
                        'path' => 'uploads/lessons/attachments/rWoewuRBFx6yUqySNWFAt7m3zNS3YnJc7yorMUNx.pdf',
                        'size' => 10527,
                        'extension' => 'pdf',
                        'date' => Carbon::now()->format('d/m/Y')
                    ]
                ],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Inserir as especialidades na base de dados
        foreach ($topicos as $topico) {
            Topic::create($topico);
        }
    }
}
