<?php

namespace Tests\Unit\Mail;

use App\Mail\NewLessonMessageMail;
use App\Models\Lesson;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NewLessonMessageMailTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_email_de_comunicado_contem_os_dados_corretos(): void
    {
        $student = User::factory()->create(['name' => 'Aluno Teste']);
        $lesson = Lesson::factory()->create(['name' => 'Aula de Laravel']);
        $message = Message::factory()->create(['subject' => 'Assunto Importante']);

        $mailable = new NewLessonMessageMail($student, $lesson, $message);

        $mailable->assertHasSubject('Novo Comunicado na Aula: Aula de Laravel');

        $mailable->assertSeeInHtml('Olá, Aluno Teste,');
        $mailable->assertSeeInHtml($lesson->name);
        $mailable->assertSeeInHtml($message->subject);

        $this->assertEquals('dashboard.emails.notification', $mailable->content()->view);
    }
}
