<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Models\File;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $coordinator;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);
        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->coordinator = User::factory()->create();
        $this->coordinator->profiles()->attach(ProfileEnum::COORDENADOR->value);
    }

    #[Test]
    public function index_displays_all_users(): void
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.users.index'));

        $response->assertOk();
        $response->assertViewIs('dashboard.users.index');
        $response->assertViewHas('users', fn($users) => $users->total() === 6);
    }

    #[Test]
    public function create_returns_create_view(): void
    {
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.users.create'));
        $response->assertOk();
        $response->assertViewIs('dashboard.users.create');
        $response->assertViewHas('profiles');
    }

    #[Test]
    public function store_creates_user_with_file_and_profiles(): void
    {
        Storage::fake('public');

        $profile = Profile::where('name', 'Professor')->first();
        $userData = User::factory()->make()->toArray();
        $userData['password'] = 'password';
        $userData['password_confirmation'] = 'password';
        $userData['profiles'] = [$profile->id];

        $response = $this->actingAs($this->coordinator)
            ->post(route('dashboard.users.store'), array_merge($userData, [
                'file' => UploadedFile::fake()->image('avatar.jpg')
            ]));

        $response->assertRedirect(route('dashboard.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['email' => $userData['email']]);

        $createdUser = User::whereEmail($userData['email'])->first();
        $this->assertTrue($createdUser->profiles->contains($profile));
        $this->assertNotNull($createdUser->file_id);
        Storage::disk('public')->assertExists($createdUser->file->path);
    }

    #[Test]
    public function store_handles_exception(): void
    {
        User::creating(fn() => throw new \Exception('Database error'));
        $userData = User::factory()->make()->toArray();
        $userData['password'] = 'password';
        $userData['password_confirmation'] = 'password';


        $response = $this->actingAs($this->coordinator)
            ->post(route('dashboard.users.store'), $userData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function edit_returns_edit_view(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.users.edit', $user));
        $response->assertOk();
        $response->assertViewIs('dashboard.users.edit');
        $response->assertViewHas('user', $user);
    }

    #[Test]
    public function update_updates_user_and_replaces_file(): void
    {
        Storage::fake('public');

        $oldFile = File::factory()->create();
        $user = User::factory()->create(['file_id' => $oldFile->id]);
        $updateData = ['name' => 'Updated Name', 'username' => 'Updated Username', 'email' => 'updated@test.com', 'cpf' => '000.000.000-00'];

        $response = $this->actingAs($this->coordinator)
            ->put(route('dashboard.users.update', $user), array_merge($updateData, [
                'file' => UploadedFile::fake()->image('new_avatar.jpg')
            ]));

        $response->assertRedirect(route('dashboard.users.index'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertNotEquals($oldFile->id, $user->file_id);
        $this->assertNotNull($user->file_id);

        Storage::disk('public')->assertExists($user->file->path);
        $this->assertDatabaseMissing('files', ['id' => $oldFile->id]);
    }

    #[Test]
    public function update_handles_exception(): void
    {
        $user = User::factory()->create();
        User::updating(fn() => throw new \Exception('Update error'));
        $updateData = ['name' => 'New Name', 'username' => 'New Username', 'cpf' => '000.000.00-00', 'email' => 'teste@teste.com'];

        $response = $this->actingAs($this->coordinator)
            ->put(route('dashboard.users.update', $user), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function destroy_deletes_user(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.users.destroy', $user));
        $response->assertRedirect(route('dashboard.users.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    #[Test]
    public function destroy_handles_exception(): void
    {
        $user = User::factory()->create();
        User::deleting(fn() => throw new \Exception('Deletion failed'));
        $response = $this->actingAs($this->coordinator)->delete(route('dashboard.users.destroy', $user));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function it_toggles_user_active_status(): void
    {
        $user = User::factory()->create(['active' => true]);
        $response = $this->actingAs($this->coordinator)->post(route('dashboard.users.active', $user));
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertFalse($user->fresh()->active);
    }

    #[Test]
    public function toggle_active_user_handles_exception(): void
    {
        $user = User::factory()->create();
        $user->saving(fn() => throw new \Exception('Save failed'));
        $response = $this->actingAs($this->coordinator)->post(route('dashboard.users.active', $user));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function it_searches_for_any_user(): void
    {
        $user = User::factory()->create(['name' => 'Searchable User']);
        $user->searchable();

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.search-user', ['q' => 'Searchable']));
        $response->assertOk();
        $response->assertJsonFragment(['text' => 'Searchable User']);
    }

    #[Test]
    public function it_searches_only_for_professors(): void
    {
        $professor = User::factory()->create(['name' => 'Searchable Professor']);
        $professor->profiles()->attach(Profile::where('name', 'Professor')->first());
        $professor->searchable();

        $student = User::factory()->create(['name' => 'Searchable Student']);
        $student->searchable();

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.search-professor', ['q' => 'Searchable']));
        $response->assertOk();
        $response->assertJsonFragment(['text' => 'Searchable Professor']);
        $response->assertJsonMissing(['text' => 'Searchable Student']);
    }
}
