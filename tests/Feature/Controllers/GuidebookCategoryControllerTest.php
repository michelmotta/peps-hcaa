<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Models\Guidebook;
use App\Models\GuidebookCategory;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuidebookCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $coordinator;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);

        $this->coordinator = User::factory()->create();
        $this->coordinator->profiles()->attach(ProfileEnum::COORDENADOR->value);
    }

    #[Test]
    public function index_displays_all_categories(): void
    {
        GuidebookCategory::factory()->count(5)->create();

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.guidebook-categories.index'));

        $response->assertOk();
        $response->assertViewHas('categories', fn($categories) => $categories->total() === 5);
    }

    #[Test]
    public function create_returns_create_view(): void
    {
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.guidebook-categories.create'));

        $response->assertOk();
        $response->assertViewIs('dashboard.guidebook-categories.create');
    }

    #[Test]
    public function store_creates_category_and_redirects(): void
    {
        $categoryData = ['name' => 'New Category'];

        $response = $this->actingAs($this->coordinator)
            ->post(route('dashboard.guidebook-categories.store'), $categoryData);

        $response->assertRedirect(route('dashboard.guidebook-categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('guidebook_categories', $categoryData);
    }

    #[Test]
    public function store_handles_exception_and_redirects_back_with_error(): void
    {
        GuidebookCategory::creating(fn() => throw new \Exception('Database error'));

        $response = $this->actingAs($this->coordinator)
            ->post(route('dashboard.guidebook-categories.store'), ['name' => 'New Category']);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function edit_returns_edit_view(): void
    {
        $category = GuidebookCategory::factory()->create();

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.guidebook-categories.edit', $category));

        $response->assertOk();
        $response->assertViewIs('dashboard.guidebook-categories.edit');
        $response->assertViewHas('category', $category);
    }

    #[Test]
    public function update_updates_category_and_redirects(): void
    {
        $category = GuidebookCategory::factory()->create();
        $updateData = ['name' => 'Updated Category Name'];

        $response = $this->actingAs($this->coordinator)
            ->put(route('dashboard.guidebook-categories.update', $category), $updateData);

        $response->assertRedirect(route('dashboard.guidebook-categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('guidebook_categories', ['id' => $category->id, 'name' => 'Updated Category Name']);
    }

    #[Test]
    public function update_handles_exception_and_redirects_back_with_error(): void
    {
        $category = GuidebookCategory::factory()->create();

        GuidebookCategory::updating(fn() => throw new \Exception('Update error'));

        $response = $this->actingAs($this->coordinator)
            ->put(route('dashboard.guidebook-categories.update', $category), ['name' => 'New Name']);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function destroy_deletes_category_if_unused(): void
    {
        $category = GuidebookCategory::factory()->create();

        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.guidebook-categories.destroy', $category));

        $response->assertRedirect(route('dashboard.guidebook-categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('guidebook_categories', ['id' => $category->id]);
    }

    #[Test]
    public function destroy_prevents_deletion_if_category_is_in_use(): void
    {
        $category = GuidebookCategory::factory()->create();
        Guidebook::factory()->create(['guidebook_category_id' => $category->id]);

        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.guidebook-categories.destroy', $category));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Não é possível apagar uma categoria que contém manuais.');
        $this->assertDatabaseHas('guidebook_categories', ['id' => $category->id]);
    }

    #[Test]
    public function destroy_handles_exception(): void
    {
        $category = GuidebookCategory::factory()->create();

        GuidebookCategory::deleting(fn() => throw new \Exception('Deletion failed'));

        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.guidebook-categories.destroy', $category));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
