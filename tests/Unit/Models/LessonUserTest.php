<?php

namespace Tests\Unit\Models;

use App\Models\Lesson;
use App\Models\LessonUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LessonUserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function os_relacionamentos_com_user_e_lesson_funcionam_corretamente(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $pivot = LessonUser::factory()->create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $this->assertInstanceOf(User::class, $pivot->user);
        $this->assertEquals($user->id, $pivot->user->id);
        $this->assertInstanceOf(Lesson::class, $pivot->lesson);
        $this->assertEquals($lesson->id, $pivot->lesson->id);
    }

    #[Test]
    public function os_acessores_de_data_e_score_formatam_os_valores_corretamente(): void
    {
        $date = Carbon::create(2025, 12, 5);
        $pivotFinished = LessonUser::factory()->create([
            'created_at' => $date,
            'finished_at' => $date,
            'score' => 85.50
        ]);
        $pivotNotFinished = LessonUser::factory()->create(['finished_at' => null, 'score' => null]);

        $this->assertEquals('05/12/2025', $pivotFinished->created_at_formatted);
        $this->assertEquals('05/12/2025', $pivotFinished->finished_at_formatted);
        $this->assertEquals('-', $pivotNotFinished->finished_at_formatted);
        $this->assertEquals('8,6', $pivotFinished->score);
        $this->assertEquals('-', $pivotNotFinished->score);
    }

    #[Test]
    public function o_mutator_parseDateAttribute_converte_formato_brasileiro(): void
    {
        $pivot = new LessonUser();
        $pivot->created_at = '25/12/2025';
        $this->assertEquals('2025-12-25', $pivot->getAttributes()['created_at']);
    }

    #[Test]
    public function o_mutator_parseDateAttribute_mantem_formato_padrao(): void
    {
        $pivot = new LessonUser();
        $now = now()->toDateTimeString();
        $pivot->created_at = $now;
        $this->assertEquals($now, $pivot->getAttributes()['created_at']);
    }
}
