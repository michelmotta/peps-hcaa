<?php

namespace Tests\Unit\Models;

use App\Models\History;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function um_historico_pertence_a_um_topico(): void
    {
        $topic = Topic::factory()->create();
        $history = History::factory()->create(['topic_id' => $topic->id]);

        $this->assertInstanceOf(Topic::class, $history->topic);
        $this->assertEquals($topic->id, $history->topic->id);
    }

    #[Test]
    public function um_historico_pertence_a_um_usuario(): void
    {
        $user = User::factory()->create();
        $history = History::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $history->user);
        $this->assertEquals($user->id, $history->user->id);
    }
}
