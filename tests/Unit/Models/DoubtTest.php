<?php

namespace Tests\Unit\Models;

use App\Models\Doubt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DoubtTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_metodo_toSearchableArray_retorna_os_dados_corretos(): void
    {
        $doubt = Doubt::factory()->create([
            'doubt' => 'Texto da dúvida para busca',
            'description' => 'Resposta do professor para a busca.',
        ]);

        $expectedArray = [
            'doubt' => 'Texto da dúvida para busca',
            'description' => 'Resposta do professor para a busca.',
        ];

        $this->assertEquals($expectedArray, $doubt->toSearchableArray());
    }

    #[Test]
    public function o_acessor_created_at_formatted_retorna_a_data_formatada(): void
    {
        $date = Carbon::create(2025, 11, 20);
        $doubt = Doubt::factory()->create(['created_at' => $date]);

        $this->assertEquals('20/11/2025', $doubt->created_at_formatted);
    }

    #[Test]
    public function o_acessor_answered_at_formatted_retorna_a_data_ou_string_vazia(): void
    {
        $date = Carbon::create(2025, 11, 21);
        $answeredDoubt = Doubt::factory()->create(['answered_at' => $date]);
        $unansweredDoubt = Doubt::factory()->create(['answered_at' => null]);

        $this->assertEquals('21/11/2025', $answeredDoubt->answered_at_formatted);
        $this->assertEquals('', $unansweredDoubt->answered_at_formatted);
    }

    #[Test]
    public function a_duvida_pertence_a_um_usuario(): void
    {
        $user = User::factory()->create();
        $doubt = Doubt::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $doubt->user);
        $this->assertEquals($user->id, $doubt->user->id);
    }
}
