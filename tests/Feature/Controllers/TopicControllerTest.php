<?php

namespace Tests\Feature\Controllers;

use App\Enums\ProfileEnum;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\Topic;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TopicControllerTest extends TestCase
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function index_displays_topics_for_a_lesson(): void
    {
        Topic::factory()->count(3)->create(['lesson_id' => $this->lesson->id]);

        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.topics.index', $this->lesson));

        $response->assertOk();
        $response->assertViewIs('dashboard.topics.index');
        $response->assertViewHas('topics', fn($topics) => $topics->total() === 3);
    }

    #[Test]
    public function create_returns_create_view(): void
    {
        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.topics.create', $this->lesson));
        $response->assertOk();
        $response->assertViewIs('dashboard.topics.create');
    }

    #[Test]
    public function store_creates_topic_with_quiz_and_redirects(): void
    {
        $video = Video::factory()->create();

        $topicData = [
            'title' => 'New Topic with Quiz',
            'description' => 'Topic content.',
            'resume' => 'New resume',
            'video_id' => $video->id,
            'quiz' => json_encode([
                ['question' => 'Q1?', 'options' => ['A', 'B'], 'correct' => 'A']
            ])
        ];

        $response = $this->actingAs($this->professor)
            ->post(route('dashboard.lessons.topics.store', $this->lesson), $topicData);

        $response->assertRedirect(route('dashboard.lessons.topics.index', $this->lesson));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('topics', ['title' => 'New Topic with Quiz']);
        $this->assertDatabaseHas('quizzes', ['question' => 'Q1?']);
    }

    #[Test]
    public function store_handles_exception(): void
    {
        $video = Video::factory()->create();
        Topic::creating(fn() => throw new \Exception('Database error'));
        $topicData = [
            'title' => 'New Topic',
            'description' => 'Topic content.',
            'resume' => 'New resume',
            'video_id' => $video->id,
        ];

        $response = $this->actingAs($this->professor)
            ->post(route('dashboard.lessons.topics.store', $this->lesson), $topicData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function edit_returns_edit_view(): void
    {
        $topic = Topic::factory()->create(['lesson_id' => $this->lesson->id]);
        $response = $this->actingAs($this->professor)->get(route('dashboard.lessons.topics.edit', [$this->lesson, $topic]));
        $response->assertOk();
        $response->assertViewIs('dashboard.topics.edit');
        $response->assertViewHas('topic', $topic);
    }

    #[Test]
    public function update_updates_topic_and_quiz_and_redirects(): void
    {
        $video = Video::factory()->create();
        $topic = Topic::factory()->create(['lesson_id' => $this->lesson->id]);
        $updateData = [
            'title' => 'Updated Topic Title',
            'description' => 'Topic content.',
            'resume' => 'New resume',
            'video_id' => $video->id,
            'quiz' => json_encode([
                ['question' => 'Updated Q?', 'options' => ['C', 'D'], 'correct' => 'D']
            ])
        ];

        $response = $this->actingAs($this->professor)
            ->put(route('dashboard.lessons.topics.update', [$this->lesson, $topic]), $updateData);

        $response->assertRedirect(route('dashboard.lessons.topics.index', $this->lesson));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('topics', ['id' => $topic->id, 'title' => 'Updated Topic Title']);
        $this->assertDatabaseHas('quizzes', ['question' => 'Updated Q?']);
    }

    #[Test]
    public function update_handles_exception(): void
    {
        $video = Video::factory()->create();
        $topic = Topic::factory()->create(['lesson_id' => $this->lesson->id]);
        Topic::updating(fn() => throw new \Exception('Update error'));
        $updateData = [
            'title' => 'New Title',
            'description' => 'Topic content.',
            'resume' => 'New resume',
            'video_id' => $video->id,
        ];

        $response = $this->actingAs($this->professor)
            ->put(route('dashboard.lessons.topics.update', [$this->lesson, $topic]), $updateData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function destroy_handles_exception(): void
    {
        $video = Video::factory()->create();
        $topic = Topic::factory()->create([
            'lesson_id' => $this->lesson->id,
            'description' => 'Topic content.',
            'resume' => 'New resume',
            'video_id' => $video->id
        ]);

        Topic::deleting(fn() => throw new \Exception('Deletion failed'));

        $response = $this->actingAs($this->professor)->delete(route('dashboard.lessons.topics.destroy', [$this->lesson, $topic]));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function attachments_upload_stores_file_and_returns_json(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->create('attachment.pdf', 100);

        $response = $this->actingAs($this->professor)->postJson(route('dashboard.attachments.upload'), [
            'file' => $file
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['date', 'name', 'extension', 'size', 'path']);
        Storage::disk('public')->assertExists($response->json('path'));
    }

    #[Test]

    public function attachments_upload_returns_error_if_no_file(): void
    {
        $response = $this->actingAs($this->professor)->postJson(route('dashboard.attachments.upload'));
        $response->assertStatus(400);
        $response->assertJson(['error' => 'No file uploaded.']);
    }
}
