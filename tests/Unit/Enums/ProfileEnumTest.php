<?php

namespace Tests\Unit\Enums;

use App\Enums\ProfileEnum;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ProfileEnumTest extends TestCase
{
    #[Test]
    public function metodo_label_retorna_os_rotulos_corretos(): void
    {
        $this->assertEquals('Coordenador', ProfileEnum::COORDENADOR->label());
        $this->assertEquals('Professor', ProfileEnum::PROFESSOR->label());
    }

    #[Test]
    public function metodo_get_id_retorna_os_ids_corretos_pelo_nome(): void
    {
        $this->assertEquals(1, ProfileEnum::getId('Coordenador'));
        $this->assertEquals(2, ProfileEnum::getId('Professor'));
    }

    #[Test]
    public function metodo_get_id_lanca_excecao_para_nome_invalido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ProfileEnum::getId('PerfilInexistente');
    }

    #[Test]
    public function metodo_get_name_retorna_os_nomes_corretos_pelo_id(): void
    {
        $this->assertEquals('Coordenador', ProfileEnum::getName(1));
        $this->assertEquals('Professor', ProfileEnum::getName(2));
    }

    #[Test]
    public function metodo_get_name_lanca_excecao_para_id_invalido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ProfileEnum::getName(99);
    }

    #[Test]
    public function os_casos_do_enum_tem_os_valores_corretos(): void
    {
        $this->assertEquals(1, ProfileEnum::COORDENADOR->value);
        $this->assertEquals(2, ProfileEnum::PROFESSOR->value);
    }
}
