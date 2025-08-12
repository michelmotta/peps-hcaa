<?php

namespace Tests\Feature\Http\Requests;

use App\Enums\ProfileEnum;
use App\Http\Controllers\UserController;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreUserRequestTest extends TestCase
{
    use RefreshDatabase;

    private User $actingUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingUser = User::factory()->create();
        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);
        $this->app['router']->post(
            '/dashboard/users',
            [UserController::class, 'store']
        )->name('dashboard.users.store')->middleware('web', 'auth');
    }

    private function getValidData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Novo Usuário',
            'email' => 'novo@example.com',
            'cpf' => '111.222.333-44',
            'username' => 'novousuario',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    #[Test]
    public function a_validacao_passa_com_dados_validos(): void
    {
        $this->actingAs($this->actingUser);
        $data = $this->getValidData();
        $response = $this->postJson(route('dashboard.users.store'), $data);
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasNoErrors();
    }

    #[Test]
    #[DataProvider('validationProvider')]
    public function a_validacao_falha_para_dados_invalidos(string $field, mixed $value, ?string $errorKey = null): void
    {
        $this->actingAs($this->actingUser);
        $data = $this->getValidData([$field => $value]);
        $response = $this->postJson(route('dashboard.users.store'), $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($errorKey ?? $field);
    }

    public static function validationProvider(): array
    {
        return [
            'name (ausente)' => ['name', ''],
            'email (ausente)' => ['email', ''],
            'cpf (ausente)' => ['cpf', ''],
            'username (ausente)' => ['username', ''],
            'password (ausente)' => ['password', ''],
            'password (curto)' => ['password', '123'],
            'profiles (id não existe)' => ['profiles', [999], 'profiles.0'],
        ];
    }

    #[Test]
    public function a_validacao_falha_se_a_senha_nao_for_confirmada(): void
    {
        $this->actingAs($this->actingUser);
        $data = $this->getValidData([
            'password' => 'password123',
            'password_confirmation' => 'valor-diferente',
        ]);

        $response = $this->postJson(route('dashboard.users.store'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function a_validacao_falha_se_o_email_ja_estiver_em_uso(): void
    {
        User::factory()->create(['email' => 'existente@example.com']);
        $this->actingAs($this->actingUser);
        $data = $this->getValidData(['email' => 'existente@example.com']);
        $response = $this->postJson(route('dashboard.users.store'), $data);
        $response->assertJsonValidationErrors('email');
    }
}
