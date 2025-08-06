<?php

namespace Tests\Unit\Models;

use App\Enums\ProfileEnum;
use App\Mail\ForgotPasswordMail;
use App\Models\Certificate;
use App\Models\File;
use App\Models\History;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $professor;

    protected function setUp(): void
    {
        parent::setUp();
        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);
        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->professor = $this->createCompleteUser();
        $this->professor->profiles()->attach(ProfileEnum::PROFESSOR->value);
    }

    /**
     * Função auxiliar para criar um usuário com todos os dados obrigatórios.
     */
    private function createCompleteUser(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'cpf' => fake()->unique()->numerify('###.###.###-##'),
            'password' => Hash::make('password'),
            'active' => true,
            'file_id' => File::factory()->create()->id,
        ], $overrides));
    }

    #[Test]
    public function o_mutator_de_senha_funciona_corretamente_em_todos_os_cenarios(): void
    {
        $user = new User();
        $user->password = 'senha123';
        $hashedPassword = $user->password;

        // Cenário 1: Criptografa uma nova senha
        $this->assertTrue(Hash::check('senha123', $hashedPassword));

        // --- CORREÇÃO PARA 100% DE COBERTURA ---
        // Cenário 2: Ignora a atribuição de uma senha nula
        $user->password = null;
        $this->assertEquals($hashedPassword, $user->password); // A senha antiga deve ser mantida
    }

    #[Test]
    public function o_metodo_toSearchableArray_retorna_os_dados_corretos(): void
    {
        $user = $this->createCompleteUser([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
            'expertise' => 'Developer',
        ]);

        $expectedArray = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
            'expertise' => 'Developer',
        ];

        $this->assertEquals($expectedArray, $user->toSearchableArray());
    }

    #[Test]
    public function os_relacionamentos_do_usuario_funcionam_corretamente(): void
    {
        $user = $this->createCompleteUser();
        $lesson = Lesson::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(File::class, $user->file);

        $user->profiles()->attach(ProfileEnum::PROFESSOR->value);
        $this->assertTrue($user->profiles->contains('name', 'Professor'));

        $this->assertTrue($user->createdLessons->contains($lesson));

        $user->subscriptions()->attach($lesson->id, ['finished' => true]);
        $this->assertTrue($user->subscriptions->contains($lesson));
        $this->assertTrue($user->completedSubscriptions->contains($lesson));
        $this->assertFalse($user->pendingSubscriptions->contains($lesson));

        // --- CORREÇÃO PARA 100% DE COBERTURA ---
        // Testa a relação studentSubscriptions
        $this->assertCount(1, $user->studentSubscriptions);

        $history = History::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->histories->contains($history));

        $certificate = Certificate::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->certificates->contains($certificate));

        UserLogin::factory()->create(['user_id' => $user->id, 'created_at' => now()->subDay()]);
        $lastLogin = UserLogin::factory()->create(['user_id' => $user->id, 'created_at' => now()]);
        $this->assertCount(2, $user->logins);
        $this->assertEquals($lastLogin->id, $user->lastLogin->id);
    }

    #[Test]
    public function os_metodos_de_verificacao_de_perfil_funcionam_corretamente(): void
    {
        $professor = $this->createCompleteUser();
        $professor->profiles()->attach(ProfileEnum::PROFESSOR->value);

        $coordinator = $this->createCompleteUser();
        $coordinator->profiles()->attach(ProfileEnum::COORDENADOR->value);

        $multiProfileUser = $this->createCompleteUser();
        $multiProfileUser->profiles()->attach([ProfileEnum::PROFESSOR->value, ProfileEnum::COORDENADOR->value]);

        $this->assertTrue($professor->hasProfile('Professor'));
        $this->assertFalse($professor->hasProfile('Coordenador'));

        $this->assertTrue($multiProfileUser->hasAnyProfile(['Professor', 'Coordenador']));
        $this->assertFalse($coordinator->hasAnyProfile(['Professor']));

        $this->assertTrue($professor->hasOnlyProfessorProfile());
        $this->assertFalse($coordinator->hasOnlyProfessorProfile());
        $this->assertFalse($multiProfileUser->hasOnlyProfessorProfile());
    }

    #[Test]
    public function o_metodo_isTeacherOf_funciona_corretamente(): void
    {
        $lesson = Lesson::factory()->create(['user_id' => $this->professor->id]);
        $anotherProfessor = $this->createCompleteUser();

        $this->assertTrue($this->professor->isTeacherOf($lesson));
        $this->assertFalse($anotherProfessor->isTeacherOf($lesson));
    }

    #[Test]
    public function o_metodo_sendPasswordResetNotification_envia_o_email_correto(): void
    {
        Mail::fake();
        $user = $this->createCompleteUser();
        $token = 'fake-token';

        $user->sendPasswordResetNotification($token);

        Mail::assertSent(ForgotPasswordMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
