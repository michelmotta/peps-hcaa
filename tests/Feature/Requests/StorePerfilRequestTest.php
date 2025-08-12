<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\StorePerfilRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StorePerfilRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Novo Usuário',
            'email' => 'novo@example.com',
            'cpf' => '111.222.333-44',
            'username' => 'novousuario',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $data = $this->getValidData([
            'biography' => 'Biografia do usuário.',
            'file' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response = $this->postJson(route('web.perfil-create'), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $data = $this->getValidData([$field => $value]);

        $response = $this->postJson(route('web.perfil-create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($field);
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
            'password (ausente)' => ['password', ''],
            'password (curto)' => ['password', '123'],
            'terms (não aceito)' => ['terms', '0'],
            'file (não é arquivo)' => ['file', 'nao-e-arquivo'],
            'file (mimetype inválido)' => ['file', UploadedFile::fake()->create('document.pdf')],
            'file (muito grande)' => ['file', UploadedFile::fake()->create('grande.jpg', 3000)],
        ];
    }

    #[Test]
    public function a_validacao_falha_se_a_senha_nao_for_confirmada(): void
    {
        $data = $this->getValidData([
            'password' => 'password123',
            'password_confirmation' => 'valor-diferente',
        ]);

        $response = $this->postJson(route('web.perfil-create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function a_validacao_falha_se_o_email_ja_estiver_em_uso(): void
    {
        User::factory()->create(['email' => 'existente@example.com']);
        $data = $this->getValidData(['email' => 'existente@example.com']);

        $response = $this->postJson(route('web.perfil-create'), $data);

        $response->assertJsonValidationErrors('email');
    }
}
