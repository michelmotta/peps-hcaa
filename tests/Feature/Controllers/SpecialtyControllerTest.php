<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Models\File;
use App\Models\Profile;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SpecialtyControllerTest extends TestCase
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
    public function index_redirects_to_first_specialty_if_none_is_selected(): void
    {
        $specialty = Specialty::factory()->create();
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.specialties.index'));
        $response->assertRedirect(route('dashboard.specialties.index', ['selected' => $specialty->id]));
    }

    #[Test]
    public function index_displays_selected_specialty(): void
    {
        $specialty = Specialty::factory()->create();
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.specialties.index', ['selected' => $specialty->id]));
        $response->assertOk();
        $response->assertViewHas('selectedSpecialty', fn($selected) => $selected->id === $specialty->id);
    }

    #[Test]
    public function index_filters_specialties_by_search_term(): void
    {
        Specialty::factory()->create(['name' => 'Matching Specialty']);
        Specialty::factory()->create(['name' => 'Another Specialty']);

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.specialties.index', ['q' => 'Matching']));
        $response->assertOk();
        $response->assertViewHas('specialties', fn($specialties) => $specialties->total() === 1 && $specialties->first()->name === 'Matching Specialty');
    }

    #[Test]
    public function create_returns_create_view(): void
    {
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.specialties.create'));
        $response->assertOk();
        $response->assertViewIs('dashboard.specialties.create');
    }

    #[Test]
    public function store_creates_specialty_with_file_and_subspecialties(): void
    {
        Storage::fake('public');
        $specialtyData = [
            'name' => 'New Specialty',
            'subspecialties' => json_encode([['value' => 'Sub 1'], ['value' => 'Sub 2']]),
            'file' => UploadedFile::fake()->image('icon.png')
        ];

        $response = $this->actingAs($this->coordinator)->post(route('dashboard.specialties.store'), $specialtyData);

        $response->assertRedirect(route('dashboard.specialties.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('specialties', ['name' => 'New Specialty']);
        $this->assertDatabaseHas('specialties', ['name' => 'Sub 1']);
        $this->assertDatabaseHas('specialties', ['name' => 'Sub 2']);

        $specialty = Specialty::where('name', 'New Specialty')->first();
        $this->assertNotNull($specialty->file_id);
        Storage::disk('public')->assertExists($specialty->file->path);
    }

    #[Test]
    public function store_handles_exception(): void
    {
        Specialty::creating(fn() => throw new \Exception('Database error'));
        $specialtyData = ['name' => 'New Specialty', 'file' => UploadedFile::fake()->image('icon.png')];

        $response = $this->actingAs($this->coordinator)->post(route('dashboard.specialties.store'), $specialtyData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function edit_returns_edit_view(): void
    {
        $specialty = Specialty::factory()->create();
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.specialties.edit', $specialty));
        $response->assertOk();
        $response->assertViewIs('dashboard.specialties.edit');
        $response->assertViewHas('specialty', $specialty);
    }

    #[Test]
    public function update_updates_specialty_and_subspecialties(): void
    {
        $specialty = Specialty::factory()->create();
        $specialty->children()->create(['name' => 'Old Sub']);
        $updateData = [
            'name' => 'Updated Specialty Name',
            'subspecialties' => json_encode([['value' => 'New Sub']])
        ];

        $response = $this->actingAs($this->coordinator)
            ->put(route('dashboard.specialties.update', $specialty), $updateData);

        $response->assertRedirect(route('dashboard.specialties.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('specialties', ['id' => $specialty->id, 'name' => 'Updated Specialty Name']);
        $this->assertDatabaseHas('specialties', ['name' => 'New Sub']);
        $this->assertDatabaseMissing('specialties', ['name' => 'Old Sub']);
    }

    #[Test]
    public function update_replaces_file(): void
    {
        Storage::fake('public');
        $oldFile = File::factory()->create();
        $specialty = Specialty::factory()->create(['file_id' => $oldFile->id]);
        $updateData = ['name' => 'Updated Name', 'file' => UploadedFile::fake()->image('new_icon.png')];

        $this->actingAs($this->coordinator)
            ->put(route('dashboard.specialties.update', $specialty), $updateData);

        $specialty->refresh();
        $this->assertNotEquals($oldFile->id, $specialty->file_id);
        Storage::disk('public')->assertExists($specialty->file->path);
        $this->assertDatabaseMissing('files', ['id' => $oldFile->id]);
    }

    #[Test]
    public function update_handles_exception(): void
    {
        $specialty = Specialty::factory()->create();
        Specialty::updating(fn() => throw new \Exception('Update error'));
        $updateData = ['name' => 'New Name'];

        $response = $this->actingAs($this->coordinator)
            ->put(route('dashboard.specialties.update', $specialty), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function destroy_deletes_specialty_and_children(): void
    {
        $specialty = Specialty::factory()->create();
        $child = $specialty->children()->create(['name' => 'Sub']);
        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.specialties.destroy', $specialty));
        $response->assertRedirect(route('dashboard.specialties.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('specialties', ['id' => $specialty->id]);
        $this->assertDatabaseMissing('specialties', ['id' => $child->id]);
    }

    #[Test]
    public function destroy_handles_exception(): void
    {
        $specialty = Specialty::factory()->create();
        Specialty::deleting(fn() => throw new \Exception('Deletion failed'));
        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.specialties.destroy', $specialty));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
