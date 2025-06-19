<?php

namespace App\Enums;

enum ProfileEnum: int
{
    case COORDENADOR = 1;
    case PROFESSOR = 2;

    public function label(): string
    {
        return match ($this) {
            self::COORDENADOR => 'Coordenador',
            self::PROFESSOR => 'Professor',
        };
    }

    public static function getId(string $profile): int
    {
        return match ($profile) {
            'Coordenador' => self::COORDENADOR->value,
            'Professor' => self::PROFESSOR->value,
            default => throw new \InvalidArgumentException('Invalid profile'),
        };
    }

    public static function getName(int $id): string
    {
        return match ($id) {
            self::COORDENADOR->value => 'Coordenador',
            self::PROFESSOR->value => 'Professor',
            default => throw new \InvalidArgumentException('Invalid profile ID'),
        };
    }
}
