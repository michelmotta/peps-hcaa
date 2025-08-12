<?php

namespace Tests\Feature\Controllers;

use App\Enums\GuidebookEnum;
use App\Enums\ProfileEnum;
use App\Models\Guidebook;
use App\Models\GuidebookCategory;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuidebookControllerTest extends TestCase
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function index_displays_all_guidebooks(): void
    {
        Guidebook::factory()->count(5)->create();

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.guidebooks.index'));

        $response->assertOk();
        $response->assertViewIs('dashboard.guidebooks.index');
        $response->assertViewHas('guidebooks', fn($guidebooks) => $guidebooks->total() === 5);
    }

    #[Test]
    public function index_filters_guidebooks_by_category(): void
    {
        $category1 = GuidebookCategory::factory()->create();
        $category2 = GuidebookCategory::factory()->create();
        Guidebook::factory()->create(['guidebook_category_id' => $category1->id]);
        Guidebook::factory()->create(['guidebook_category_id' => $category2->id]);

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.guidebooks.index', ['category_id' => $category1->id]));

        $response->assertOk();
        $response->assertViewHas('guidebooks', fn($guidebooks) => $guidebooks->total() === 1 && $guidebooks->first()->guidebook_category_id === $category1->id);
    }

    #[Test]
    public function create_returns_create_view(): void
    {
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.guidebooks.create'));
        $response->assertOk();
        $response->assertViewIs('dashboard.guidebooks.create');
        $response->assertViewHas('categories');
    }

    #[Test]
    public function store_creates_guidebook_and_redirects(): void
    {
        $category = GuidebookCategory::factory()->create();
        $guidebookData = ['title' => 'New Guidebook', 'description' => 'Some content', 'guidebook_category_id' => $category->id, 'type' => GuidebookEnum::INTERN->value];

        $response = $this->actingAs($this->coordinator)
            ->post(route('dashboard.guidebooks.store'), $guidebookData);

        $response->assertRedirect(route('dashboard.guidebooks.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('guidebooks', ['title' => 'New Guidebook']);
    }

    #[Test]
    public function store_handles_exception(): void
    {
        Guidebook::creating(fn() => throw new \Exception('Database error'));
        $category = GuidebookCategory::factory()->create();
        $guidebookData = ['title' => 'New Guidebook', 'description' => 'Some content', 'guidebook_category_id' => $category->id, 'type' => GuidebookEnum::INTERN->value];

        $response = $this->actingAs($this->coordinator)
            ->post(route('dashboard.guidebooks.store'), $guidebookData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function edit_returns_edit_view(): void
    {
        $guidebook = Guidebook::factory()->create();
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.guidebooks.edit', $guidebook));
        $response->assertOk();
        $response->assertViewIs('dashboard.guidebooks.edit');
        $response->assertViewHas('guidebook', $guidebook);
    }

    #[Test]
    public function update_updates_guidebook_and_redirects(): void
    {
        $guidebook = Guidebook::factory()->create();
        $updateData = ['title' => 'Updated Guidebook Title', 'description' => 'Updated content', 'guidebook_category_id' => $guidebook->guidebook_category_id, 'type' => GuidebookEnum::INTERN->value];

        $response = $this->actingAs($this->coordinator)
            ->put(route('dashboard.guidebooks.update', $guidebook), $updateData);

        $response->assertRedirect(route('dashboard.guidebooks.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('guidebooks', ['id' => $guidebook->id, 'title' => 'Updated Guidebook Title']);
    }

    #[Test]
    public function update_handles_exception(): void
    {
        $guidebook = Guidebook::factory()->create();
        Guidebook::updating(fn() => throw new \Exception('Update error'));
        $updateData = ['title' => 'Updated Guidebook Title', 'description' => 'Updated content', 'guidebook_category_id' => $guidebook->guidebook_category_id, 'type' => GuidebookEnum::INTERN->value];

        $response = $this->actingAs($this->coordinator)
            ->put(route('dashboard.guidebooks.update', $guidebook), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function destroy_deletes_guidebook_and_redirects(): void
    {
        $guidebook = Guidebook::factory()->create();
        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.guidebooks.destroy', $guidebook));

        $response->assertRedirect(route('dashboard.guidebooks.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('guidebooks', ['id' => $guidebook->id]);
    }

    #[Test]
    public function destroy_handles_exception(): void
    {
        $guidebook = Guidebook::factory()->create();
        Guidebook::deleting(fn() => throw new \Exception('Deletion failed'));
        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.guidebooks.destroy', $guidebook));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
