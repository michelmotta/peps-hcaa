<?php

namespace Tests\Unit\Models;

use App\Models\Certificate;
use App\Models\Doubt;
use App\Models\Feedback;
use App\Models\File;
use App\Models\Lesson;
use App\Models\Message;
use App\Models\Specialty;
use App\Models\Topic;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LessonTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_metodo_toSearchableArray_retorna_os_dados_corretos(): void
    {
        $lesson = Lesson::factory()->create([
            'name' => 'Título da Aula para Busca',
            'description' => 'Descrição da aula para a busca.',
        ]);

        $expectedArray = [
            'name' => 'Título da Aula para Busca',
            'description' => 'Descrição da aula para a busca.',
        ];

        $this->assertEquals($expectedArray, $lesson->toSearchableArray());
    }

    #[Test]
    public function o_acessor_created_at_formatted_retorna_a_data_formatada(): void
    {
        $date = Carbon::create(2025, 7, 15);
        $lesson = Lesson::factory()->create(['created_at' => $date]);

        $this->assertEquals('15/07/2025', $lesson->created_at_formatted);
    }

    #[Test]
    public function os_relacionamentos_da_aula_funcionam_corretamente(): void
    {
        $teacher = User::factory()->create();
        $lesson = Lesson::factory()->create(['user_id' => $teacher->id]);
        $student = User::factory()->create();

        $this->assertInstanceOf(File::class, $lesson->file);
        $this->assertInstanceOf(User::class, $lesson->teacher);
        $this->assertEquals($teacher->id, $lesson->teacher->id);

        Topic::factory(2)->create(['lesson_id' => $lesson->id]);

        Doubt::factory(2)->create(['lesson_id' => $lesson->id, 'answered' => true]);
        Doubt::factory(1)->create(['lesson_id' => $lesson->id, 'answered' => false]);

        Feedback::factory(4)->create(['lesson_id' => $lesson->id]);
        Certificate::factory(5)->create(['lesson_id' => $lesson->id]);
        Message::factory(6)->create(['lesson_id' => $lesson->id]);

        $this->assertCount(2, $lesson->topics);
        $this->assertCount(3, $lesson->doubts);
        $this->assertCount(2, $lesson->answeredDoubts);
        $this->assertCount(4, $lesson->feedbacks);
        $this->assertCount(5, $lesson->certificates);
        $this->assertCount(6, $lesson->messages);

        $lesson->subscriptions()->attach($student->id, ['finished' => true]);
        $this->assertCount(1, $lesson->subscriptions);
        $this->assertCount(1, $lesson->completedSubscriptions);

        $specialty = Specialty::factory()->create();
        $lesson->specialties()->attach($specialty->id);
        $this->assertTrue($lesson->specialties->contains($specialty));
    }

    #[Test]
    public function o_acessor_total_duration_calcula_a_duracao_corretamente(): void
    {
        $lesson = Lesson::factory()->create();

        $video = Video::factory()->create(['duration' => '01:10:05']);
        Topic::factory()->create([
            'lesson_id' => $lesson->id,
            'video_id' => $video->id,
        ]);

        $expectedDuration = '1h 10m 5s';

        $this->assertEquals($expectedDuration, $lesson->fresh()->total_duration);
    }
}
