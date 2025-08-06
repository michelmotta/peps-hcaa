<?php

namespace Tests\Unit\Enums;

use App\Enums\LessonStatusEnum;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class LessonStatusEnumTest extends TestCase
{
    #[Test]
    public function o_metodo_get_lesson_status_retorna_o_array_correto(): void
    {
        $expectedArray = [
            1 => 'Rascunho',
            2 => 'Aguardando',
            3 => 'Publicada',
        ];

        $statusEnum = LessonStatusEnum::RASCUNHO;
        $this->assertEquals($expectedArray, $statusEnum->getLessonStatus());
    }

    #[Test]
    public function o_metodo_get_lesson_status_id_by_name_retorna_os_ids_corretos(): void
    {
        $this->assertEquals(1, LessonStatusEnum::getLessonStatusIdByName('Rascunho'));
        $this->assertEquals(2, LessonStatusEnum::getLessonStatusIdByName('Aguardando'));
        $this->assertEquals(3, LessonStatusEnum::getLessonStatusIdByName('Publicada'));
    }

    #[Test]
    public function o_metodo_get_lesson_status_id_by_name_lanca_excecao_para_nome_invalido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LessonStatusEnum::getLessonStatusIdByName('StatusInexistente');
    }

    #[Test]
    public function o_metodo_get_lesson_status_name_by_id_retorna_os_nomes_corretos(): void
    {
        $this->assertEquals('Rascunho', LessonStatusEnum::getLessonStatusNameById(1));
        $this->assertEquals('Aguardando', LessonStatusEnum::getLessonStatusNameById(2));
        $this->assertEquals('Publicada', LessonStatusEnum::getLessonStatusNameById(3));
    }

    #[Test]
    public function o_metodo_get_lesson_status_name_by_id_lanca_excecao_para_id_invalido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        LessonStatusEnum::getLessonStatusNameById(99);
    }

    #[Test]
    public function os_casos_do_enum_tem_os_valores_corretos(): void
    {
        $this->assertEquals(1, LessonStatusEnum::RASCUNHO->value);
        $this->assertEquals(2, LessonStatusEnum::AGUARDANDO_PUBLICACAO->value);
        $this->assertEquals(3, LessonStatusEnum::PUBLICADA->value);
    }
}
