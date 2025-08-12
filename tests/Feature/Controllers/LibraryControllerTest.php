<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Models\File;
use App\Models\Library;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LibraryControllerTest extends TestCase
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
    public function index_displays_all_library_items(): void
    {
        Library::factory()->count(5)->create();

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.libraries.index'));

        $response->assertOk();
        $response->assertViewIs('dashboard.libraries.index');
        $response->assertViewHas('libraries', fn($libraries) => $libraries->total() === 5);
    }

    #[Test]
    public function index_filters_library_items_by_search_term(): void
    {
        Library::factory()->create(['title' => 'Matching Library Item']);
        Library::factory()->create(['title' => 'Another Library Item']);

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.libraries.index', ['q' => 'Matching']));

        $response->assertOk();
    }

    #[Test]
    public function create_returns_create_view(): void
    {
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.libraries.create'));
        $response->assertOk();
        $response->assertViewIs('dashboard.libraries.create');
    }

    #[Test]
    public function store_creates_library_item_with_file_and_redirects(): void
    {
        Storage::fake('public');

        $libraryData = ['title' => 'New Library Item'];
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->coordinator)
            ->post(route('dashboard.libraries.store'), array_merge($libraryData, ['file' => $file]));

        $response->assertRedirect(route('dashboard.libraries.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('libraries', ['title' => 'New Library Item']);

        $libraryItem = Library::first();
        $this->assertNotNull($libraryItem->file_id);
        Storage::disk('public')->assertExists($libraryItem->file->path);
    }

    #[Test]
    public function store_handles_exception(): void
    {
        Library::creating(fn() => throw new \Exception('Database error'));
        $libraryData = ['title' => 'New Library Item', 'file' => UploadedFile::fake()->create('document.pdf', 100)];

        $response = $this->actingAs($this->coordinator)
            ->post(route('dashboard.libraries.store'), $libraryData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function edit_returns_edit_view(): void
    {
        $library = Library::factory()->create();
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.libraries.edit', $library));
        $response->assertOk();
        $response->assertViewIs('dashboard.libraries.edit');
        $response->assertViewHas('library', $library);
    }

    #[Test]
    public function update_updates_library_item_and_replaces_file(): void
    {
        Storage::fake('public');
        $oldFile = File::factory()->create();
        $library = Library::factory()->create(['file_id' => $oldFile->id]);
        $updateData = ['title' => 'Updated Library Item'];
        $newFile = UploadedFile::fake()->create('new_document.pdf', 200);

        $response = $this->actingAs($this->coordinator)
            ->put(route('dashboard.libraries.update', $library), array_merge($updateData, ['file' => $newFile]));

        $response->assertRedirect(route('dashboard.libraries.index'));
        $response->assertSessionHas('success');

        $library->refresh();
        $this->assertNotEquals($oldFile->id, $library->file_id);
        $this->assertNotNull($library->file_id);

        Storage::disk('public')->assertExists($library->file->path);
        $this->assertDatabaseMissing('files', ['id' => $oldFile->id]);
    }

    #[Test]
    public function update_handles_exception(): void
    {
        $library = Library::factory()->create();
        Library::updating(fn() => throw new \Exception('Update error'));
        $updateData = ['title' => 'New Name'];

        $response = $this->actingAs($this->coordinator)
            ->put(route('dashboard.libraries.update', $library), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function destroy_deletes_library_item(): void
    {
        $library = Library::factory()->create();
        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.libraries.destroy', $library));
        $response->assertRedirect(route('dashboard.libraries.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('libraries', ['id' => $library->id]);
    }

    #[Test]
    public function destroy_handles_exception(): void
    {
        $library = Library::factory()->create();
        Library::deleting(fn() => throw new \Exception('Deletion failed'));
        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.libraries.destroy', $library));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
