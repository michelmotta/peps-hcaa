<?php

namespace App\Enums;

enum LessonStatusEnum: int
{
    case RASCUNHO = 1;
    case AGUARDANDO_PUBLICACAO = 2;
    case PUBLICADA = 3;

    public function getLessonStatus(): array
    {
        return [
            self::RASCUNHO->value => 'Rascunho',
            self::AGUARDANDO_PUBLICACAO->value => 'Aguardando',
            self::PUBLICADA->value => 'Publicada',
        ];
    }

    public static function getLessonStatusIdByName(string $status): int
    {
        return match ($status) {
            'Rascunho' => self::RASCUNHO->value,
            'Aguardando' => self::AGUARDANDO_PUBLICACAO->value,
            'Publicada' => self::PUBLICADA->value,
            default => throw new \InvalidArgumentException('Invalid status'),
        };
    }

    public static function getLessonStatusNameById(int $id): string
    {
        return match ($id) {
            self::RASCUNHO->value => 'Rascunho',
            self::AGUARDANDO_PUBLICACAO->value => 'Aguardando',
            self::PUBLICADA->value => 'Publicada',
            default => throw new \InvalidArgumentException('Invalid profile ID'),
        };
    }
}
