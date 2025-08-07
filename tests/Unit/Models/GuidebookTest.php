<?php

namespace Tests\Unit\Models;

use App\Enums\GuidebookEnum;
use App\Models\Guidebook;
use App\Models\GuidebookCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuidebookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_metodo_toSearchableArray_retorna_os_dados_corretos(): void
    {
        $guidebook = Guidebook::factory()->create([
            'title' => 'Guia de Procedimentos de UTI',
            'description' => 'Descrição detalhada do guia.',
        ]);

        $expectedArray = [
            'title' => 'Guia de Procedimentos de UTI',
            'description' => 'Descrição detalhada do guia.',
        ];

        $this->assertEquals($expectedArray, $guidebook->toSearchableArray());
    }

    #[Test]
    public function o_guia_pertence_a_uma_categoria(): void
    {
        $category = GuidebookCategory::factory()->create();
        $guidebook = Guidebook::factory()->create(['guidebook_category_id' => $category->id]);

        $this->assertInstanceOf(GuidebookCategory::class, $guidebook->category);
        $this->assertEquals($category->id, $guidebook->category->id);
    }

    #[Test]
    public function o_atributo_type_e_convertido_para_o_enum_correto(): void
    {
        $guidebook = Guidebook::factory()->create(['type' => GuidebookEnum::INTERN]);

        $this->assertInstanceOf(GuidebookEnum::class, $guidebook->type);
        $this->assertEquals(GuidebookEnum::INTERN, $guidebook->type);
    }
}
