<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginUserRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /**
     * Define um usuário base para os testes.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'username' => 'usuario.teste',
        ]);
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'username' => $this->user->username,
            'password' => 'password',
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $data = $this->getValidData();

        $response = $this->post(route('login-post'), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value): void
    {
        $data = $this->getValidData([$field => $value]);

        $response = $this->post(route('login-post'), $data);

        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasErrors($field);
    }

    public static function validationProvider(): array
    {
        return [
            'username (ausente)' => ['username', ''],
            'username (não existe)' => ['username', 'usuario.inexistente'],
            'password (ausente)' => ['password', ''],
            'password (curto)' => ['password', '123'],
        ];
    }
}
