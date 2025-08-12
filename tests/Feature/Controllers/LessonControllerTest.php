<?php

namespace Tests\Feature\Controllers;

use App\Enums\LessonStatusEnum;
use App\Enums\ProfileEnum;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LessonControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $coordinator;
    private User $professor;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);
        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->coordinator = User::factory()->create();
        $this->coordinator->profiles()->attach(ProfileEnum::COORDENADOR->value);

        $this->professor = User::factory()->create();
        $this->professor->profiles()->attach(ProfileEnum::PROFESSOR->value);
    }

    #[Test]
    public function index_displays_all_lessons_for_coordinator(): void
    {
        Lesson::factory()->count(5)->create(['user_id' => $this->professor->id]);
        Lesson::factory()->count(3)->create(['user_id' => $this->coordinator->id]);

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.lessons.index'));

        $response->assertOk();
        $response->assertViewHas('lessons', fn($lessons) => $lessons->total() === 8);
    }

    #[Test]
    public function index_displays_only_own_lessons_for_professor(): void
    {
        Lesson::factory()->count(5)->create(['user_id' => $this->professor->id]);
        Lesson::factory()->count(3)->create(['user_id' => $this->coordinator->id]);

        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.index'));

        $response->assertOk();
        $response->assertViewHas('lessons', fn($lessons) => $lessons->total() === 5);
    }

    #[Test]
    public function index_filters_lessons_by_status_and_specialty(): void
    {
        $specialty = Specialty::factory()->create();
        Lesson::factory()->create(['lesson_status' => LessonStatusEnum::PUBLICADA->value])->specialties()->attach($specialty->id);
        Lesson::factory()->create(['lesson_status' => LessonStatusEnum::RASCUNHO->value]);

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.lessons.index', [
            'status' => LessonStatusEnum::PUBLICADA->value,
            'specialty' => $specialty->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('lessons', fn($lessons) => $lessons->total() === 1);
    }

    #[Test]
    public function create_returns_create_view_with_specialties(): void
    {
        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.create'));

        $response->assertOk();
        $response->assertViewIs('dashboard.lessons.create');
        $response->assertViewHas('specialties');
    }

    #[Test]
    public function edit_returns_edit_view_for_authorized_user(): void
    {
        $lesson = Lesson::factory()->create(['user_id' => $this->professor->id]);

        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.edit', $lesson));

        $response->assertOk();
        $response->assertViewIs('dashboard.lessons.edit');
        $response->assertViewHas('lesson', $lesson);
    }

    #[Test]
    public function edit_is_forbidden_for_unauthorized_professor(): void
    {
        $lesson = Lesson::factory()->create(['user_id' => $this->coordinator->id]);

        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.edit', $lesson));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function update_updates_lesson_and_redirects(): void
    {
        $lesson = Lesson::factory()->create(['user_id' => $this->professor->id]);
        $updateData = ['name' => 'Updated Lesson Name', 'description' => "Descrição", 'workload' => 10];

        $response = $this->actingAs($this->professor)
            ->put(route('dashboard.lessons.update', $lesson), $updateData);

        $response->assertRedirect(route('dashboard.lessons.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lessons', ['id' => $lesson->id, 'name' => 'Updated Lesson Name']);
    }

    #[Test]
    public function destroy_deletes_lesson_for_authorized_user(): void
    {
        $lesson = Lesson::factory()->create(['user_id' => $this->professor->id]);

        $response = $this->actingAs($this->professor)->delete(route('dashboard.lessons.destroy', $lesson));

        $response->assertRedirect(route('dashboard.lessons.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
    }

    #[Test]
    public function change_status_updates_lesson_status(): void
    {
        $lesson = Lesson::factory()->create(['lesson_status' => LessonStatusEnum::RASCUNHO->value]);

        $response = $this->actingAs($this->coordinator)->post(
            route('dashboard.lessons.change-status', $lesson),
            ['status_id' => LessonStatusEnum::PUBLICADA->value]
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('lessons', [
            'id' => $lesson->id,
            'lesson_status' => LessonStatusEnum::PUBLICADA->value
        ]);
    }

    #[Test]
    public function it_denies_certificate_generation_for_unauthorized_user(): void
    {
        $lesson = Lesson::factory()->create();
        $unauthorizedUser = User::factory()->create();

        $response = $this->actingAs($this->coordinator)
            ->get(route('dashboard.lessons.certificates', ['lesson' => $lesson, 'user' => $unauthorizedUser]));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Você não tem permissão para gerar este certificado.');
    }
}
