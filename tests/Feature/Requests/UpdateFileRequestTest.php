<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\UpdateFileRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateFileRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->put('/test-update-file', function (UpdateFileRequest $request) {
            return response()->json(['success' => true]);
        });
    }

    #[Test]
    public function o_metodo_authorize_impede_o_acesso_a_requisicao(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->putJson('/test-update-file');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    #[Test]
    public function o_metodo_rules_esta_vazio_e_nao_aplica_regras_de_validacao(): void
    {
        $request = new UpdateFileRequest();

        $this->assertEquals([], $request->rules());
    }
}
