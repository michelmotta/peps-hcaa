<?php

namespace Tests\Feature\Http\Requests;

use App\Http\Requests\StoreHistoryRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreHistoryRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->post('/test-store-history', function (StoreHistoryRequest $request) {
            return response()->json(['success' => true]);
        });
    }

    #[Test]
    public function the_authorize_method_denies_access(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/test-store-history');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    #[Test]
    public function the_rules_method_is_empty(): void
    {
        $request = new StoreHistoryRequest();

        $this->assertEquals([], $request->rules());
    }
}
