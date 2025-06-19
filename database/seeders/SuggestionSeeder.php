<?php

namespace Database\Seeders;

use App\Models\Suggestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuggestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suggestions = [
            [
                'name' => 'Manejo Avançado de Sepse na UTI',
                'description' => 'Estratégias atualizadas para diagnóstico precoce, antibioticoterapia e suporte hemodinâmico no tratamento de pacientes sépticos em unidades de terapia intensiva.',
            ],
            [
                'name' => 'Sedação e Analgesia em Pacientes Críticos',
                'description' => 'Protocolos modernos para sedação consciente, monitoramento de dor e prevenção da síndrome de abstinência em pacientes de longa permanência na UTI.',
            ],
            [
                'name' => 'Ventilação Mecânica: Boas Práticas e Desmame',
                'description' => 'Abordagens para otimizar o suporte ventilatório, prevenir lesões pulmonares induzidas por ventilação e protocolos de desmame eficazes.',
            ],
            [
                'name' => 'Cuidados com Cateteres e Prevenção de Infecções',
                'description' => 'Diretrizes de inserção, manutenção e retirada de cateteres vasculares para reduzir a incidência de infecções associadas à assistência em saúde (IRAS).',
            ],
            [
                'name' => 'Manejo da Insuficiência Renal Aguda na UTI',
                'description' => 'Critérios para início de terapia renal substitutiva, tipos de diálise indicados e cuidados na monitorização hemodinâmica de pacientes críticos.',
            ],
            [
                'name' => 'Comunicação Efetiva com Familiares na UTI',
                'description' => 'Técnicas de comunicação compassiva para fornecer informações claras, alinhar expectativas e apoiar famílias durante a internação de seus entes queridos.',
            ],
        ];

        foreach ($suggestions as $suggestion) {
            Suggestion::create([
                'name' => $suggestion['name'],
                'description' => $suggestion['description'],
                'votes' => rand(1, 20),
                'user_id' => rand(1, 4),
            ]);
        }
    }
}
