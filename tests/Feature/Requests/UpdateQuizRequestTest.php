<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\UpdateQuizRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateQuizRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->put('/test-update-quiz', function (UpdateQuizRequest $request) {
            return response()->json(['success' => true]);
        });
    }

    #[Test]
    public function o_metodo_authorize_impede_o_acesso_a_requisicao(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->putJson('/test-update-quiz');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    #[Test]
    public function o_metodo_rules_esta_vazio_e_nao_aplica_regras_de_validacao(): void
    {
        $request = new UpdateQuizRequest();

        $this->assertEquals([], $request->rules());
    }
}
