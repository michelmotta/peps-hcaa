<?php

namespace App\Enums;

enum GuidebookEnum: string
{
    case INTERN = 'intern';
    case EXTERN = 'extern';

    public function label(): string
    {
        return match ($this) {
            self::INTERN => 'Uso Interno',
            self::EXTERN => 'Uso Externo',
        };
    }
}
