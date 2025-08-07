<?php

namespace Tests\Unit\Models;

use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Support\Str;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_metodo_toSearchableArray_retorna_os_dados_corretos(): void
    {

        $message = Message::factory()->create([
            'subject' => 'Assunto para Busca',
            'description' => 'Descrição para a busca.',
        ]);

        $expectedArray = [
            'subject' => 'Assunto para Busca',
            'description' => 'Descrição para a busca.',
        ];

        $this->assertIsArray($message->toSearchableArray());
    }

    #[Test]
    public function o_acessor_created_at_formatted_retorna_a_data_formatada(): void
    {
        $date = Carbon::create(2025, 8, 5, 14, 30, 0);
        $message = Message::factory()->create(['created_at' => $date]);

        $this->assertEquals('05/08/2025', $message->created_at_formatted);
    }

    #[Test]
    public function a_mensagem_pertence_a_um_usuario(): void
    {
        $user = User::factory()->create();
        $message = Message::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $message->user);
        $this->assertEquals($user->id, $message->user->id);
    }

    #[Test]
    public function o_acessor_description_resume_retorna_o_resumo_correto(): void
    {
        $longDescription = 'Este é um texto de descrição muito longo que definitivamente tem mais de cem caracteres para que possamos testar a funcionalidade do limitador de string do Laravel.';
        $message = Message::factory()->create(['description' => $longDescription]);

        $expectedResume = Str::limit($longDescription, 100);

        $this->assertEquals($expectedResume, $message->description_resume);
    }
}
