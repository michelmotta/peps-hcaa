<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Models\Lesson;
use App\Models\LessonUser;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class LessonUserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $coordinator;
    protected User $student;
    protected Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate([
            'id' => ProfileEnum::COORDENADOR->value,
            'name' => 'Coordenador'
        ]);

        $this->coordinator = User::factory()->create();
        $this->coordinator->profiles()->attach(ProfileEnum::COORDENADOR->value);

        $this->student = User::factory()->create();
        $this->lesson = Lesson::factory()->create();
    }

    public function test_index_without_search_and_with_search()
    {
        LessonUser::factory()->create([
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->student->id,
            'finished' => false,
            'created_at' => Carbon::now(),
        ]);

        $this->actingAs($this->coordinator)
            ->get(route('dashboard.lessons.subscriptions.index', $this->lesson))
            ->assertStatus(200)
            ->assertViewHas('subscriptions');

        $this->actingAs($this->coordinator)
            ->get(route('dashboard.lessons.subscriptions.index', [
                'lesson' => $this->lesson,
                'q' => $this->student->name
            ]))
            ->assertStatus(200)
            ->assertViewHas('subscriptions');
    }

    public function test_create()
    {
        $this->actingAs($this->coordinator)
            ->get(route('dashboard.lessons.subscriptions.create', $this->lesson))
            ->assertStatus(200);
    }

    public function test_store_success()
    {
        $data = [
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->student->id,
            'finished' => true,
            'created_at' => Carbon::now(),
        ];

        $this->actingAs($this->coordinator)
            ->post(route('dashboard.lessons.subscriptions.store', $this->lesson), $data)
            ->assertRedirect(route('dashboard.lessons.subscriptions.index', $this->lesson))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('lesson_user', [
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->student->id,
            'finished' => true
        ]);
    }

    public function test_store_failure_due_to_invalid_user()
    {
        $this->actingAs($this->coordinator)
            ->post(route('dashboard.lessons.subscriptions.store', $this->lesson), [
                'lesson_id' => $this->lesson->id,
                'user_id' => 999999,
                'finished' => false,
                'created_at' => Carbon::now(),
            ])
            ->assertSessionHas('error');
    }

    public function test_edit()
    {
        $subscription = LessonUser::factory()->create([
            'id' => 1,
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->student->id,
            'finished' => false,
            'created_at' => Carbon::now(),
        ]);

        $this->actingAs($this->coordinator)
            ->get(route('dashboard.lessons.subscriptions.edit', [$this->lesson, $subscription]))
            ->assertStatus(200);
    }

    public function test_destroy_failure_due_to_invalid_id()
    {
        $this->actingAs($this->coordinator)
            ->delete(route('dashboard.lessons.subscriptions.destroy', [$this->lesson, 999999]))
            ->assertNotFound();
    }

    public function test_subscribe_not_logged_in()
    {
        $this->post(route('web.subscribe', $this->lesson))
            ->assertRedirect();
    }

    public function test_subscribe_new_subscription()
    {
        $this->actingAs($this->student)
            ->post(route('web.subscribe', $this->lesson))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertTrue(
            $this->student->subscriptions()->where('lesson_id', $this->lesson->id)->exists()
        );
    }

    public function test_subscribe_already_subscribed()
    {
        $this->student->subscriptions()->attach($this->lesson->id, [
            'finished' => false,
            'created_at' => Carbon::now()
        ]);

        $this->actingAs($this->student)
            ->post(route('web.subscribe', $this->lesson))
            ->assertRedirect()
            ->assertSessionHas('error');
    }
}
