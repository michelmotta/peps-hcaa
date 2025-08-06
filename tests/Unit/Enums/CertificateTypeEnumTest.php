<?php

namespace Tests\Unit;

use App\Enums\CertificateTypeEnum;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CertificateTypeEnumTest extends TestCase
{
    #[Test]
    public function o_caso_student_retorna_o_rotulo_correto(): void
    {
        $label = CertificateTypeEnum::STUDENT->label();

        $this->assertEquals('Estudante', $label);
    }

    #[Test]
    public function o_caso_teacher_retorna_o_rotulo_correto(): void
    {
        $label = CertificateTypeEnum::TEACHER->label();

        $this->assertEquals('Professor', $label);
    }

    #[Test]
    public function os_valores_dos_casos_estao_corretos(): void
    {
        $this->assertEquals('student', CertificateTypeEnum::STUDENT->value);
        $this->assertEquals('teacher', CertificateTypeEnum::TEACHER->value);
    }
}
