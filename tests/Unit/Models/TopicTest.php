<?php

namespace Tests\Unit\Models;

use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Topic;
use App\Models\Video;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TopicTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function o_topico_pertence_a_uma_aula(): void
    {
        $lesson = Lesson::factory()->create();
        $topic = Topic::factory()->create(['lesson_id' => $lesson->id]);

        $this->assertInstanceOf(Lesson::class, $topic->lesson);
        $this->assertEquals($lesson->id, $topic->lesson->id);
    }

    #[Test]
    public function o_topico_pertence_a_um_video(): void
    {
        $video = Video::factory()->create();
        $topic = Topic::factory()->create(['video_id' => $video->id]);

        $this->assertInstanceOf(Video::class, $topic->video);
        $this->assertEquals($video->id, $topic->video->id);
    }

    #[Test]
    public function o_topico_pode_ter_muitos_quizzes(): void
    {
        $topic = Topic::factory()->create();
        Quiz::factory(3)->create(['topic_id' => $topic->id]);

        $this->assertInstanceOf(Collection::class, $topic->quizzes);
        $this->assertCount(3, $topic->quizzes);
    }

    #[Test]
    public function o_metodo_toSearchableArray_retorna_os_dados_corretos(): void
    {
        $topic = Topic::factory()->create([
            'title' => 'Título do Tópico para Busca',
            'description' => 'Descrição detalhada para a busca.',
        ]);

        $expectedArray = [
            'title' => 'Título do Tópico para Busca',
            'description' => 'Descrição detalhada para a busca.',
        ];

        $this->assertEquals($expectedArray, $topic->toSearchableArray());
    }
}
