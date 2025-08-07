<?php

namespace Tests\Unit\Models;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function um_perfil_pode_ter_muitos_usuarios(): void
    {
        $profile = Profile::factory()->create();
        $users = User::factory(3)->create();

        $profile->users()->attach($users->pluck('id'));

        $this->assertInstanceOf(Collection::class, $profile->users);

        $this->assertCount(3, $profile->users);
    }
}
