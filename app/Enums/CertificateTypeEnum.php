<?php

namespace App\Enums;

enum CertificateTypeEnum: string
{
    case STUDENT = 'student';
    case TEACHER = 'teacher';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT => 'Estudante',
            self::TEACHER => 'Professor',
        };
    }
}
