<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Models\Profile;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SuggestionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);

        $this->user = User::factory()->create();
        $this->user->profiles()->attach(ProfileEnum::COORDENADOR->value);
    }

    #[Test]
    public function index_displays_all_suggestions(): void
    {
        Suggestion::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get(route('dashboard.suggestions.index'));

        $response->assertOk();
        $response->assertViewIs('dashboard.suggestions.index');
        $response->assertViewHas('suggestions', fn($suggestions) => $suggestions->total() === 5);
    }

    #[Test]
    public function index_filters_suggestions_by_search_term(): void
    {
        Suggestion::factory()->create(['name' => 'Matching Suggestion']);
        Suggestion::factory()->create(['name' => 'Another Suggestion']);


        $response = $this->actingAs($this->user)->get(route('dashboard.suggestions.index', ['q' => 'Matching']));

        $response->assertOk();
    }

    #[Test]
    public function create_returns_create_view(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.suggestions.create'));
        $response->assertOk();
        $response->assertViewIs('dashboard.suggestions.create');
    }

    #[Test]
    public function store_creates_suggestion_and_redirects(): void
    {
        $suggestionData = ['name' => 'New Suggestion', 'description' => 'A great idea.', 'votes' => 5];

        $response = $this->actingAs($this->user)
            ->post(route('dashboard.suggestions.store'), $suggestionData);

        $response->assertRedirect(route('dashboard.suggestions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suggestions', ['name' => 'New Suggestion', 'user_id' => $this->user->id]);
    }

    #[Test]
    public function store_handles_exception(): void
    {
        Suggestion::creating(fn() => throw new \Exception('Database error'));
        $suggestionData = ['name' => 'New Suggestion', 'description' => 'A great idea.', 'votes' => 5];

        $response = $this->actingAs($this->user)
            ->post(route('dashboard.suggestions.store'), $suggestionData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function edit_returns_edit_view(): void
    {
        $suggestion = Suggestion::factory()->create();
        $response = $this->actingAs($this->user)->get(route('dashboard.suggestions.edit', $suggestion));
        $response->assertOk();
        $response->assertViewIs('dashboard.suggestions.edit');
        $response->assertViewHas('suggestion', $suggestion);
    }

    #[Test]
    public function update_updates_suggestion_and_redirects(): void
    {
        $suggestion = Suggestion::factory()->create();
        $updateData = ['name' => 'Updated Suggestion Title', 'description' => 'Updated description.', 'votes' => 5];

        $response = $this->actingAs($this->user)
            ->put(route('dashboard.suggestions.update', $suggestion), $updateData);

        $response->assertRedirect(route('dashboard.suggestions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suggestions', ['id' => $suggestion->id, 'name' => 'Updated Suggestion Title']);
    }

    #[Test]
    public function update_handles_exception(): void
    {
        $suggestion = Suggestion::factory()->create();
        Suggestion::updating(fn() => throw new \Exception('Update error'));
        $updateData = ['name' => 'New Title', 'description' => 'New description', 'votes' => 5];

        $response = $this->actingAs($this->user)
            ->put(route('dashboard.suggestions.update', $suggestion), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function destroy_deletes_suggestion(): void
    {
        $suggestion = Suggestion::factory()->create();
        $response = $this->actingAs($this->user)->delete(route('dashboard.suggestions.destroy', $suggestion));
        $response->assertRedirect(route('dashboard.suggestions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('suggestions', ['id' => $suggestion->id]);
    }

    #[Test]
    public function destroy_handles_exception(): void
    {
        $suggestion = Suggestion::factory()->create();
        Suggestion::deleting(fn() => throw new \Exception('Deletion failed'));
        $response = $this->actingAs($this->user)->delete(route('dashboard.suggestions.destroy', $suggestion));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
