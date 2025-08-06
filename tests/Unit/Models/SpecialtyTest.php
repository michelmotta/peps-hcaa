<?php

namespace Tests\Unit\Models;

use App\Models\File;
use App\Models\Lesson;
use App\Models\Specialty;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SpecialtyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_metodo_toSearchableArray_retorna_os_dados_corretos(): void
    {
        $parent = Specialty::factory()->create();
        $specialty = Specialty::factory()->create([
            'name' => 'Cardiologia Pediátrica',
            'parent_id' => $parent->id,
        ]);

        $expectedArray = [
            'id' => $specialty->id,
            'name' => 'Cardiologia Pediátrica',
            'parent_id' => $parent->id,
        ];

        $this->assertEquals($expectedArray, $specialty->toSearchableArray());
    }

    #[Test]
    public function a_especialidade_pertence_a_um_arquivo(): void
    {
        $file = File::factory()->create();
        $specialty = Specialty::factory()->create(['file_id' => $file->id]);

        $this->assertInstanceOf(File::class, $specialty->file);
        $this->assertEquals($file->id, $specialty->file->id);
    }

    #[Test]
    public function uma_especialidade_pode_ter_uma_especialidade_pai(): void
    {
        $parent = Specialty::factory()->create();
        $child = Specialty::factory()->create(['parent_id' => $parent->id]);

        $this->assertInstanceOf(Specialty::class, $child->parent);
        $this->assertEquals($parent->id, $child->parent->id);
    }

    #[Test]
    public function uma_especialidade_pode_ter_muitas_especialidades_filhas(): void
    {
        $parent = Specialty::factory()->create();
        Specialty::factory(3)->create(['parent_id' => $parent->id]);

        $this->assertInstanceOf(Collection::class, $parent->children);
        $this->assertCount(3, $parent->children);
    }

    #[Test]
    public function uma_especialidade_pode_estar_associada_a_muitas_aulas(): void
    {
        $specialty = Specialty::factory()->create();
        $lessons = Lesson::factory(2)->create();

        $specialty->lessons()->attach($lessons->pluck('id'));

        $this->assertInstanceOf(Collection::class, $specialty->lessons);
        $this->assertCount(2, $specialty->lessons);
    }
}
