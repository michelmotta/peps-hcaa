<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Models\Doubt;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DoubtControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $professor;
    private User $student;
    private Lesson $lesson;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->professor = User::factory()->create();
        $this->professor->profiles()->attach(ProfileEnum::PROFESSOR->value);

        $this->student = User::factory()->create();

        $this->lesson = Lesson::factory()->create(['user_id' => $this->professor->id]);
    }

    #[Test]
    public function index_displays_doubts_for_a_lesson(): void
    {
        Doubt::factory()->count(3)->create(['lesson_id' => $this->lesson->id]);

        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.doubts.index', $this->lesson));

        $response->assertOk();
        $response->assertViewIs('dashboard.doubts.index');
        $response->assertViewHas('doubts', fn($doubts) => $doubts->total() === 3);
    }

    #[Test]
    public function index_filters_doubts_by_search_term(): void
    {
        Doubt::factory()->create(['doubt' => 'Matching Doubt', 'lesson_id' => $this->lesson->id]);
        Doubt::factory()->create(['doubt' => 'Another Doubt', 'lesson_id' => $this->lesson->id]);

        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.doubts.index', [
            'lesson' => $this->lesson,
            'q' => 'Matching'
        ]));

        $response->assertOk();
    }

    #[Test]
    public function create_returns_create_view(): void
    {
        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.doubts.create', $this->lesson));
        $response->assertOk();
        $response->assertViewIs('dashboard.doubts.create');
    }

    #[Test]
    public function store_creates_answered_doubt_and_redirects(): void
    {
        $doubtData = ['doubt' => 'A new doubt', 'description' => 'Here is the answer.'];

        $response = $this->actingAs($this->professor)
            ->post(route('dashboard.lessons.doubts.store', $this->lesson), $doubtData);

        $response->assertRedirect(route('dashboard.lessons.doubts.index', $this->lesson));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('doubts', ['doubt' => 'A new doubt', 'answered' => true]);
    }

    #[Test]
    public function store_creates_unanswered_doubt(): void
    {
        $doubtData = ['doubt' => 'An unanswered doubt', 'description' => ' '];

        $response = $this->actingAs($this->professor)
            ->post(route('dashboard.lessons.doubts.store', $this->lesson), $doubtData);

        $this->assertDatabaseHas('doubts', ['doubt' => 'An unanswered doubt', 'answered' => false, 'answered_at' => null]);
    }

    #[Test]
    public function store_handles_exception(): void
    {
        Doubt::creating(fn() => throw new \Exception('Database error'));
        $doubtData = ['doubt' => 'A new doubt', 'description' => 'An answer.'];

        $response = $this->actingAs($this->professor)
            ->post(route('dashboard.lessons.doubts.store', $this->lesson), $doubtData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function edit_returns_edit_view(): void
    {
        $doubt = Doubt::factory()->create(['lesson_id' => $this->lesson->id]);
        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.doubts.edit', [$this->lesson, $doubt]));
        $response->assertOk();
        $response->assertViewIs('dashboard.doubts.edit');
        $response->assertViewHas('doubt', $doubt);
    }

    #[Test]
    public function update_updates_doubt_to_answered_and_redirects(): void
    {
        $doubt = Doubt::factory()->create(['lesson_id' => $this->lesson->id, 'answered' => false]);
        $updateData = ['doubt' => 'Updated Doubt', 'description' => 'Now it is answered.'];

        $response = $this->actingAs($this->professor)
            ->put(route('dashboard.lessons.doubts.update', [$this->lesson, $doubt]), $updateData);

        $response->assertRedirect(route('dashboard.lessons.doubts.index', $this->lesson));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('doubts', ['id' => $doubt->id, 'doubt' => 'Updated Doubt', 'answered' => true]);
    }

    #[Test]
    public function update_updates_doubt_to_unanswered(): void
    {
        $doubt = Doubt::factory()->create(['lesson_id' => $this->lesson->id, 'answered' => true]);
        $updateData = ['doubt' => 'Updated Doubt', 'description' => ' ']; // Empty description

        $this->actingAs($this->professor)
            ->put(route('dashboard.lessons.doubts.update', [$this->lesson, $doubt]), $updateData);

        $this->assertDatabaseHas('doubts', ['id' => $doubt->id, 'answered' => false, 'answered_at' => null]);
    }

    #[Test]
    public function update_handles_exception(): void
    {
        $doubt = Doubt::factory()->create(['lesson_id' => $this->lesson->id]);
        Doubt::updating(fn() => throw new \Exception('Update error'));
        $updateData = ['doubt' => 'New Doubt Text'];

        $response = $this->actingAs($this->professor)
            ->put(route('dashboard.lessons.doubts.update', [$this->lesson, $doubt]), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function destroy_deletes_doubt(): void
    {
        $doubt = Doubt::factory()->create(['lesson_id' => $this->lesson->id]);
        $response = $this->actingAs($this->professor)->delete(route('dashboard.lessons.doubts.destroy', [$this->lesson, $doubt]));
        $response->assertRedirect(route('dashboard.lessons.doubts.index', $this->lesson));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('doubts', ['id' => $doubt->id]);
    }

    #[Test]
    public function destroy_handles_exception(): void
    {
        $doubt = Doubt::factory()->create(['lesson_id' => $this->lesson->id]);
        Doubt::deleting(fn() => throw new \Exception('Deletion failed'));
        $response = $this->actingAs($this->professor)->delete(route('dashboard.lessons.doubts.destroy', [$this->lesson, $doubt]));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function student_can_create_a_doubt_via_ajax(): void
    {
        $doubtData = ['doubt' => 'This is a student doubt.'];

        $response = $this->actingAs($this->student)
            ->postJson(route('web.doubt-create', $this->lesson), $doubtData);

        $response->assertOk();
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('doubts', [
            'doubt' => 'This is a student doubt.',
            'user_id' => $this->student->id,
            'lesson_id' => $this->lesson->id
        ]);
    }

    #[Test]
    public function doubt_create_handles_exception(): void
    {
        Doubt::creating(fn() => throw new \Exception('AJAX create error'));
        $doubtData = ['doubt' => 'This will fail.'];

        $response = $this->actingAs($this->student)
            ->postJson(route('web.doubt-create', $this->lesson), $doubtData);

        $response->assertStatus(500);
        $response->assertJson(['status' => 'error']);
    }
}
