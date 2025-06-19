<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessons = [
            [
                'name' => 'Emergências Cardiológicas em UTI',
                'description' => 'Manejo intensivo de condições cardiovasculares críticas como infarto agudo do miocárdio, choque cardiogênico e arritmias graves em pacientes internados na UTI.',
                'lesson_status' => 3,
                'workload' => 10,
                'file_id' => 10,
                'user_id' => 1,
                'specialty_id' => 1, // Cardiologia
            ],
            [
                'name' => 'Cuidados Intensivos em Pediatria',
                'description' => 'Abordagem de casos pediátricos graves na UTI, incluindo sepse, insuficiência respiratória e suporte hemodinâmico em crianças criticamente enfermas.',
                'lesson_status' => 3,
                'workload' => 20,
                'file_id' => 11,
                'user_id' => 2,
                'specialty_id' => 2, // Pediatria
            ],
            [
                'name' => 'Traumas Ortopédicos em Pacientes Críticos',
                'description' => 'Condutas e protocolos para estabilização e monitoramento de pacientes politraumatizados com fraturas e lesões ortopédicas graves em ambiente de UTI.',
                'lesson_status' => 1,
                'workload' => 30,
                'file_id' => 12,
                'user_id' => 3,
                'specialty_id' => 3, // Ortopedia
            ],
            [
                'name' => 'Complicações Dermatológicas em Terapia Intensiva',
                'description' => 'Identificação e manejo de lesões cutâneas em pacientes críticos, como úlceras por pressão, necrose e reações adversas a medicamentos na UTI.',
                'lesson_status' => 1,
                'workload' => 40,
                'file_id' => 13,
                'user_id' => 3,
                'specialty_id' => 4, // Dermatologia
            ],
        ];

        foreach ($lessons as $lesson) {
            Lesson::create($lesson);
        }
    }
}
