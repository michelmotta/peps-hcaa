<?php

namespace Tests\Feature\Http\Requests;

use App\Enums\ProfileEnum;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateUserRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $coordinator;
    private User $userToUpdate;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);
        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->coordinator = User::factory()->create();
        $this->coordinator->profiles()->attach(ProfileEnum::COORDENADOR->value);

        $this->userToUpdate = User::factory()->create();
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Nome Válido',
            'email' => 'valido@email.com',
            'cpf' => '123.456.789-00',
            'username' => 'validouser',
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_todos_os_campos_validos(): void
    {
        $this->actingAs($this->coordinator);
        $data = $this->getValidData([
            'password' => 'novaSenha123',
            'password_confirmation' => 'novaSenha123',
            'biography' => 'Uma biografia.',
            'expertise' => 'Uma especialidade.',
            'profiles' => [ProfileEnum::PROFESSOR->value],
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response = $this->putJson(route('dashboard.users.update', $this->userToUpdate), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value, ?string $errorKey = null): void
    {
        $this->actingAs($this->coordinator);
        $data = $this->getValidData([$field => $value]);

        $response = $this->putJson(route('dashboard.users.update', $this->userToUpdate), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($errorKey ?? $field);
    }

    public static function validationProvider(): array
    {
        return [
            'name (ausente)' => ['name', ''],
            'name (longo)' => ['name', str_repeat('a', 256)],
            'email (ausente)' => ['email', ''],
            'email (inválido)' => ['email', 'email-invalido'],
            'cpf (ausente)' => ['cpf', ''],
            'username (ausente)' => ['username', ''],
            'password (curto)' => ['password', '123'],
            'password (não confirmado)' => ['password', 'password123'],
            'profiles (não é array)' => ['profiles', 'nao-e-array'],
            'profiles (id não existe)' => ['profiles', [999], 'profiles.0'],
            'file (não é arquivo)' => ['file', 'nao-e-arquivo'],
            'file (mimetype inválido)' => ['file', UploadedFile::fake()->create('document.pdf')],
            'file (muito grande)' => ['file', UploadedFile::fake()->create('grande.jpg', 3000)],
        ];
    }

    #[Test]
    public function a_validacao_de_campos_unicos_ignora_o_usuario_atual(): void
    {
        $this->actingAs($this->coordinator);
        $data = [
            'name' => 'Nome Válido',
            'email' => $this->userToUpdate->email,
            'cpf' => $this->userToUpdate->cpf,
            'username' => $this->userToUpdate->username,
        ];

        $response = $this->putJson(route('dashboard.users.update', $this->userToUpdate), $data);
        $response->assertSessionHasNoErrors();
    }
}
