<?php

namespace Tests\Unit\Models;

use App\Models\Guidebook;
use App\Models\GuidebookCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuidebookCategoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function uma_categoria_pode_ter_muitos_guias(): void
    {
        $category = GuidebookCategory::factory()->create();
        Guidebook::factory(3)->create(['guidebook_category_id' => $category->id]);

        $this->assertInstanceOf(Collection::class, $category->guidebooks);

        $this->assertCount(3, $category->guidebooks);
    }
}
