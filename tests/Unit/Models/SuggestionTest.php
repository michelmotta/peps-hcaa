<?php

namespace Tests\Unit\Models;

use App\Models\Suggestion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuggestionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_metodo_toSearchableArray_retorna_os_dados_corretos(): void
    {
        $suggestion = Suggestion::factory()->create([
            'name' => 'Sugestão de Título para Busca',
            'description' => 'Descrição da sugestão para a busca.',
        ]);

        $expectedArray = [
            'name' => 'Sugestão de Título para Busca',
            'description' => 'Descrição da sugestão para a busca.',
        ];

        $this->assertEquals($expectedArray, $suggestion->toSearchableArray());
    }

    #[Test]
    public function o_acessor_created_at_formatted_retorna_a_data_formatada(): void
    {
        $date = Carbon::create(2025, 1, 25, 10, 30, 0);
        $suggestion = Suggestion::factory()->create(['created_at' => $date]);

        $this->assertEquals('25/01/2025', $suggestion->created_at_formatted);
    }

    #[Test]
    public function a_sugestao_pertence_a_um_usuario(): void
    {
        $user = User::factory()->create();
        $suggestion = Suggestion::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $suggestion->user);
        $this->assertEquals($user->id, $suggestion->user->id);
    }
}
