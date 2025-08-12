<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Mail\NewLessonMessageMail;
use App\Models\Lesson;
use App\Models\Message;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $professor;
    private Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->professor = User::factory()->create();
        $this->professor->profiles()->attach(ProfileEnum::PROFESSOR->value);

        $this->lesson = Lesson::factory()->create(['user_id' => $this->professor->id]);
    }

    #[Test]
    public function index_displays_messages_for_a_lesson(): void
    {
        Message::factory()->count(3)->create(['lesson_id' => $this->lesson->id]);

        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.messages.index', $this->lesson));

        $response->assertOk();
        $response->assertViewIs('dashboard.messages.index');
        $response->assertViewHas('messages', fn($messages) => $messages->total() === 3);
    }

    #[Test]
    public function index_filters_messages_by_search_term(): void
    {
        Message::factory()->create(['subject' => 'Matching Message', 'lesson_id' => $this->lesson->id]);
        Message::factory()->create(['subject' => 'Another Message', 'lesson_id' => $this->lesson->id]);

        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.messages.index', [
            'lesson' => $this->lesson,
            'q' => 'Matching'
        ]));

        $response->assertOk();
    }

    #[Test]
    public function create_returns_create_view(): void
    {
        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.messages.create', $this->lesson));
        $response->assertOk();
        $response->assertViewIs('dashboard.messages.create');
    }

    #[Test]
    public function store_creates_message_sends_email_and_redirects(): void
    {
        Mail::fake();

        $student = User::factory()->create();
        $this->lesson->subscriptions()->attach($student->id);

        $messageData = ['subject' => 'New Message', 'description' => 'Important update.'];

        $response = $this->actingAs($this->professor)
            ->post(route('dashboard.lessons.messages.store', $this->lesson), $messageData);

        $response->assertRedirect(route('dashboard.lessons.messages.index', $this->lesson));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('messages', ['subject' => 'New Message']);

        Mail::assertSent(NewLessonMessageMail::class, function ($mail) use ($student) {
            return $mail->hasTo($student->email);
        });
    }

    #[Test]
    public function store_handles_exception(): void
    {
        Message::creating(fn() => throw new \Exception('Database error'));
        $messageData = ['subject' => 'New Message', 'description' => 'Important update.'];

        $response = $this->actingAs($this->professor)
            ->post(route('dashboard.lessons.messages.store', $this->lesson), $messageData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function edit_returns_edit_view(): void
    {
        $message = Message::factory()->create(['lesson_id' => $this->lesson->id]);
        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.messages.edit', [$this->lesson, $message]));
        $response->assertOk();
        $response->assertViewIs('dashboard.messages.edit');
        $response->assertViewHas('message', $message);
    }

    #[Test]
    public function update_updates_message_sends_email_and_redirects(): void
    {
        Mail::fake();
        $student = User::factory()->create();
        $this->lesson->subscriptions()->attach($student->id);

        $message = Message::factory()->create(['lesson_id' => $this->lesson->id]);
        $updateData = ['subject' => 'Updated Message Title', 'description' => 'Updated content.'];

        $response = $this->actingAs($this->professor)
            ->put(route('dashboard.lessons.messages.update', [$this->lesson, $message]), $updateData);

        $response->assertRedirect(route('dashboard.lessons.messages.index', $this->lesson));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('messages', ['id' => $message->id, 'subject' => 'Updated Message Title']);

        Mail::assertSent(NewLessonMessageMail::class);
    }

    #[Test]
    public function update_handles_exception(): void
    {
        $message = Message::factory()->create(['lesson_id' => $this->lesson->id]);
        Message::updating(fn() => throw new \Exception('Update error'));
        $updateData = ['subject' => 'New Title', 'description' => 'Required description'];

        $response = $this->actingAs($this->professor)
            ->put(route('dashboard.lessons.messages.update', [$this->lesson, $message]), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function destroy_deletes_message(): void
    {
        $message = Message::factory()->create(['lesson_id' => $this->lesson->id]);
        $response = $this->actingAs($this->professor)->delete(route('dashboard.lessons.messages.destroy', [$this->lesson, $message]));
        $response->assertRedirect(route('dashboard.lessons.messages.index', $this->lesson));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('messages', ['id' => $message->id]);
    }

    #[Test]
    public function destroy_handles_exception(): void
    {
        $message = Message::factory()->create(['lesson_id' => $this->lesson->id]);
        Message::deleting(fn() => throw new \Exception('Deletion failed'));
        $response = $this->actingAs($this->professor)->delete(route('dashboard.lessons.messages.destroy', [$this->lesson, $message]));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
