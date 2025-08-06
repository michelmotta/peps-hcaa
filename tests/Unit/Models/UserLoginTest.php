<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function um_registro_de_login_pertence_a_um_usuario(): void
    {
        $user = User::factory()->create();
        $loginRecord = UserLogin::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $loginRecord->user);

        $this->assertEquals($user->id, $loginRecord->user->id);
    }
}
