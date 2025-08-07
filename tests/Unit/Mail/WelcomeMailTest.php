<?php

namespace Tests\Unit\Mail;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WelcomeMailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_email_de_boas_vindas_contem_os_dados_corretos(): void
    {
        $user = User::factory()->create([
            'name' => 'Usuário de Teste',
        ]);

        $mailable = new WelcomeMail($user);

        $mailable->assertHasSubject('Bem-vindo(a) ao PEPS!');

        $mailable->assertSeeInHtml('Olá, Usuário de Teste,');

        $this->assertEquals('dashboard.emails.welcome', $mailable->content()->view);
    }
}
