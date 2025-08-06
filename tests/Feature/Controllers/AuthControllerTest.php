<?php

namespace Tests\Feature;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'cpf' => '000.000.000-00',
            'password' => Hash::make('password'),
            'active' => true,
        ]);
    }

    #[Test]
    public function usuario_pode_logar_com_credenciais_validas(): void
    {
        $response = $this->post(route('login'), [
            'username' => $this->user->username,
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($this->user);
    }

    #[Test]
    public function usuario_inativo_nao_pode_logar(): void
    {
        $inactiveUser = $this->user;

        $inactiveUser->active = false;
        $inactiveUser->save();

        $response = $this->post(route('login'), [
            'username' => $inactiveUser->username,
            'password' => 'password',
        ]);

        $response->assertSessionHas('error', 'O usuário informado está inativo. Entre em contato com um coordenador.');
        $this->assertGuest();
    }

    #[Test]
    public function usuario_pode_deslogar_com_sucesso(): void
    {
        $this->actingAs($this->user);

        $response = $this->post(route('logout-post'));

        $response->assertRedirect(route('web.index'));
        $this->assertGuest();
    }

    #[Test]
    public function usuario_pode_se_cadastrar_com_upload_de_arquivo(): void
    {
        Mail::fake();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $userData = [
            'name' => 'Novo Usuário',
            'email' => 'novo@example.com',
            'username' => 'novousuario',
            'cpf' => '111.222.333-44',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
            'file' => $file,
        ];

        $response = $this->post(route('web.perfil-create'), $userData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('login'));
        $this->assertDatabaseHas('users', ['email' => 'novo@example.com']);
        Mail::assertSent(WelcomeMail::class);

        Storage::disk('public')->assertExists('uploads/users/' . $file->hashName());
    }

    #[Test]
    public function usuario_pode_atualizar_o_proprio_perfil(): void
    {
        $this->actingAs($this->user);

        $response = $this->patch(route('web.perfil-update', $this->user), [
            'name' => 'Updated Name',
            'email' => $this->user->email,
            'cpf' => $this->user->cpf,
            'username' => $this->user->username,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('web.perfil'));
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'name' => 'Updated Name']);
    }

    #[Test]
    public function usuario_nao_pode_atualizar_o_perfil_de_outros(): void
    {
        $otherUser = User::factory()->create([
            'name' => 'Outro Usuario',
            'username' => 'outrousuario',
            'email' => 'outro@example.com',
            'cpf' => '111.111.111-11',
            'password' => Hash::make('password'),
            'active' => true,
        ]);

        $this->actingAs($this->user);

        $response = $this->patch(route('web.perfil-update', $otherUser), [
            'name' => 'Hacked',
            'email' => $otherUser->email,
            'cpf' => $otherUser->cpf,
            'username' => $otherUser->username,
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseMissing('users', ['id' => $otherUser->id, 'name' => 'Hacked']);
    }

    #[Test]
    public function link_de_redefinicao_de_senha_e_enviado(): void
    {
        $response = $this->post(route('password.forgot'), ['email' => $this->user->email]);

        $response->assertSessionHas('status');
    }

    #[Test]
    public function senha_pode_ser_redefinida_com_token_valido(): void
    {
        $token = Password::createToken($this->user);

        $response = $this->post(route('password.reset', ['token' => $token]), [
            'token' => $token,
            'email' => $this->user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('newpassword', $this->user->fresh()->password));
    }
}
