<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $coordinator;
    private User $student;
    private User $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);
        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->coordinator = User::factory()->create();
        $this->coordinator->profiles()->attach(ProfileEnum::COORDENADOR->value);

        $this->teacher = User::factory()->create();
        $this->teacher->profiles()->attach(ProfileEnum::PROFESSOR->value);

        $this->student = User::factory()->create();
    }

    #[Test]
    public function report_by_student_view_is_rendered_successfully(): void
    {
        $response = $this->actingAs($this->coordinator)
            ->get(route('dashboard.reports.students'));

        $response->assertOk();
        $response->assertViewIs('dashboard.reports.report_student');
        $response->assertViewHas('student', null);
    }

    #[Test]
    public function report_by_student_shows_correct_data_for_selected_student(): void
    {
        $lesson1 = Lesson::factory()->create(['workload' => 10]);
        $lesson2 = Lesson::factory()->create(['workload' => 5]);
        $this->student->subscriptions()->attach($lesson1->id, ['finished' => true]);
        $this->student->subscriptions()->attach($lesson2->id, ['finished' => false]);

        $response = $this->actingAs($this->coordinator)
            ->get(route('dashboard.reports.students', ['student_id' => $this->student->id]));

        $response->assertOk();
        $response->assertViewIs('dashboard.reports.report_student');
        $response->assertViewHas('student', fn($viewStudent) => $viewStudent->id === $this->student->id);
        $response->assertViewHas('subscriptions', fn($subs) => $subs->count() === 2);
        $response->assertViewHas('completedWorkload', 10);
    }

    #[Test]
    public function report_by_student_filters_data_by_date_range(): void
    {
        $this->student->subscriptions()->attach(Lesson::factory()->create()->id, ['created_at' => now()->startOfDay()]);
        $this->student->subscriptions()->attach(Lesson::factory()->create()->id, ['created_at' => now()->subDay()]);
        $this->student->subscriptions()->attach(Lesson::factory()->create()->id, ['created_at' => now()->subWeek()]);

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.reports.students', [
            'student_id' => $this->student->id,
            'start_date' => now()->subDays(2)->toDateString(),
            'end_date' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertViewHas('subscriptions', fn($subs) => $subs->total() === 2);
    }

    #[Test]
    public function it_exports_student_report_as_a_pdf(): void
    {
        Pdf::shouldReceive('loadView')->once()->andReturnSelf();
        Pdf::shouldReceive('stream')->once()->andReturn(new Response('fake pdf content', 200, ['Content-Type' => 'application/pdf']));

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.reports.students.export', [
            'student_id' => $this->student->id
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function export_student_pdf_fails_validation_without_student_id(): void
    {
        $response = $this->actingAs($this->coordinator)
            ->get(route('dashboard.reports.students.export'));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('student_id');
    }

    #[Test]
    public function report_by_teacher_view_is_rendered_successfully(): void
    {
        $response = $this->actingAs($this->coordinator)
            ->get(route('dashboard.reports.teachers'));

        $response->assertOk();
        $response->assertViewIs('dashboard.reports.report_teacher');
        $response->assertViewHas('teacher', null);
    }

    #[Test]
    public function report_by_teacher_shows_correct_data_for_selected_teacher(): void
    {
        Lesson::factory()->count(3)->create(['user_id' => $this->teacher->id]);

        $response = $this->actingAs($this->coordinator)
            ->get(route('dashboard.reports.teachers', ['teacher_id' => $this->teacher->id]));

        $response->assertOk();
        $response->assertViewIs('dashboard.reports.report_teacher');
        $response->assertViewHas('teacher', fn($viewTeacher) => $viewTeacher->id === $this->teacher->id);
        $response->assertViewHas('lessons', fn($lessons) => $lessons->total() === 3);
        $response->assertViewHas('stats', fn($stats) => $stats['created_lessons_count'] === 3);
    }

    #[Test]
    public function it_exports_teacher_report_as_a_pdf(): void
    {
        Pdf::shouldReceive('loadView')->once()->andReturnSelf();
        Pdf::shouldReceive('stream')->once()->andReturn(new Response('fake pdf content', 200, ['Content-Type' => 'application/pdf']));

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.reports.teachers.export', [
            'teacher_id' => $this->teacher->id
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function report_by_lesson_view_is_rendered_successfully(): void
    {
        $response = $this->actingAs($this->coordinator)
            ->get(route('dashboard.reports.lessons'));

        $response->assertOk();
        $response->assertViewIs('dashboard.reports.report_lesson');
        $response->assertViewHas('lesson', null);
    }

    #[Test]
    public function report_by_lesson_shows_correct_data_for_selected_lesson(): void
    {
        $lesson = Lesson::factory()->create();
        User::factory()->count(5)->create()->each(function ($user) use ($lesson) {
            $lesson->subscriptions()->attach($user->id);
        });

        $response = $this->actingAs($this->coordinator)
            ->get(route('dashboard.reports.lessons', ['lesson_id' => $lesson->id]));

        $response->assertOk();
        $response->assertViewIs('dashboard.reports.report_lesson');
        $response->assertViewHas('lesson', fn($viewLesson) => $viewLesson->id === $lesson->id);
        $response->assertViewHas('students', fn($students) => $students->total() === 5);
    }

    #[Test]
    public function it_exports_lesson_report_as_a_pdf(): void
    {
        $lesson = Lesson::factory()->create();
        Pdf::shouldReceive('loadView')->once()->andReturnSelf();
        Pdf::shouldReceive('stream')->once()->andReturn(new Response('fake pdf content', 200, ['Content-Type' => 'application/pdf']));

        $response = $this->actingAs($this->coordinator)->get(route('dashboard.reports.lessons.export', [
            'lesson_id' => $lesson->id
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[Test]
    public function export_lesson_pdf_fails_validation_if_lesson_not_found(): void
    {
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.reports.lessons.export', [
            'lesson_id' => 999
        ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('lesson_id');
    }
}
