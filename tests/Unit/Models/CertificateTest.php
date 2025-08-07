<?php

namespace Tests\Unit\Models;

use App\Enums\CertificateTypeEnum;
use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CertificateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function os_relacionamentos_com_user_e_lesson_funcionam_corretamente(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $certificate = Certificate::factory()->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->assertInstanceOf(User::class, $certificate->user);
        $this->assertEquals($user->id, $certificate->user->id);

        $this->assertInstanceOf(Lesson::class, $certificate->lesson);
        $this->assertEquals($lesson->id, $certificate->lesson->id);
    }

    #[Test]
    public function o_metodo_registerCertificate_cria_um_novo_certificado_se_nao_existir(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $certificate = Certificate::registerCertificate($lesson, $user, CertificateTypeEnum::STUDENT);

        $this->assertInstanceOf(Certificate::class, $certificate);
        $this->assertDatabaseHas('certificates', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'type' => CertificateTypeEnum::STUDENT->value,
        ]);
    }

    #[Test]
    public function o_metodo_registerCertificate_atualiza_um_certificado_existente(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $initialDate = now()->subDay();
        $existingCertificate = Certificate::factory()->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'type' => CertificateTypeEnum::STUDENT->value,
            'issued_at' => $initialDate,
        ]);

        $updatedCertificate = Certificate::registerCertificate($lesson, $user, CertificateTypeEnum::STUDENT);

        $this->assertDatabaseCount('certificates', 1);

        $this->assertEquals($existingCertificate->id, $updatedCertificate->id);

        $this->assertNotEquals($initialDate->toDateTimeString(), $updatedCertificate->issued_at->toDateTimeString());
    }
}
