<?php

namespace Tests\Unit;

use App\Enums\GuidebookEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GuidebookEnumTest extends TestCase
{
    #[Test]
    public function o_caso_intern_retorna_o_rotulo_correto(): void
    {
        $label = GuidebookEnum::INTERN->label();
        $this->assertEquals('Uso Interno', $label);
    }

    #[Test]
    public function o_caso_extern_retorna_o_rotulo_correto(): void
    {
        $label = GuidebookEnum::EXTERN->label();
        $this->assertEquals('Uso Externo', $label);
    }

    #[Test]
    public function os_valores_dos_casos_estao_corretos(): void
    {
        $this->assertEquals('intern', GuidebookEnum::INTERN->value);
        $this->assertEquals('extern', GuidebookEnum::EXTERN->value);
    }
}
