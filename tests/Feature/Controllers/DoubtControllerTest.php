<?php

namespace Tests\Feature\Dashboard;

use App\Enums\ProfileEnum;
use App\Models\Doubt;
use App\Models\File;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DoubtControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $professor;
    protected User $student;
    protected Lesson $lesson;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);
        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->professor = $this->createCompleteUser();
        $this->professor->profiles()->attach(ProfileEnum::PROFESSOR->value);

        $this->student = $this->createCompleteUser();

        $this->lesson = $this->createCompleteLesson(['user_id' => $this->professor->id]);
    }

    #[Test]
    public function professor_pode_visualizar_a_lista_de_duvidas_de_sua_aula(): void
    {
        Doubt::factory()->create(['lesson_id' => $this->lesson->id, 'user_id' => $this->student->id]);

        $response = $this->actingAs($this->professor)
            ->get(route('dashboard.lessons.doubts.index', $this->lesson));

        $response->assertOk();
        $response->assertViewIs('dashboard.doubts.index');
        $response->assertViewHas('doubts', function ($doubts) {
            return $doubts->count() === 1;
        });
    }

    #[Test]
    public function professor_pode_responder_uma_duvida_ao_atualiza_la(): void
    {
        $doubt = Doubt::factory()->create([
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->student->id,
            'answered' => false,
            'answered_at' => null,
            'description' => null,
        ]);

        $updateData = [
            'doubt' => 'Texto da dúvida original',
            'description' => 'Esta é a resposta do professor.',
        ];

        $response = $this->actingAs($this->professor)
            ->put(route('dashboard.lessons.doubts.update', [$this->lesson, $doubt]), $updateData);

        $response->assertRedirect(route('dashboard.lessons.doubts.index', $this->lesson));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('doubts', [
            'id' => $doubt->id,
            'answered' => true,
        ]);
        $this->assertNotNull($doubt->fresh()->answered_at);
    }

    #[Test]
    public function professor_pode_apagar_uma_duvida(): void
    {
        $doubt = Doubt::factory()->create([
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->student->id,
            'answered' => false,
            'answered_at' => null,
            'description' => null,
        ]);

        $response = $this->actingAs($this->professor)
            ->delete(route('dashboard.lessons.doubts.destroy', [$this->lesson, $doubt]));

        $response->assertRedirect(route('dashboard.lessons.doubts.index', $this->lesson));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('doubts', ['id' => $doubt->id]);
    }

    #[Test]
    public function estudante_pode_criar_uma_duvida_em_uma_aula(): void
    {
        $doubtData = [
            'doubt' => 'Qual é o procedimento padrão para este caso?',
        ];

        $response = $this->actingAs($this->student)
            ->postJson(route('web.doubt-create', $this->lesson), $doubtData);

        $response->assertOk();
        $response->assertJsonPath('status', 'success');
        $this->assertDatabaseHas('doubts', [
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->student->id,
            'doubt' => 'Qual é o procedimento padrão para este caso?',
            'answered' => false,
        ]);
    }

    #[Test]
    public function estudante_nao_pode_acessar_rotas_de_duvidas_do_dashboard(): void
    {
        $doubt = Doubt::factory()->create([
            'lesson_id' => $this->lesson->id,
            'user_id' => $this->student->id,
            'answered' => false,
            'answered_at' => null,
            'description' => null,
        ]);

        $responseIndex = $this->actingAs($this->student)
            ->get(route('dashboard.lessons.doubts.index', $this->lesson));
        $responseEdit = $this->actingAs($this->student)
            ->get(route('dashboard.lessons.doubts.edit', [$this->lesson, $doubt]));

        $responseIndex->assertForbidden();
        $responseEdit->assertForbidden();
    }

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

    private function createCompleteLesson(array $overrides = []): Lesson
    {
        return Lesson::factory()->create(array_merge([
            'name' => 'Aula de Teste: ' . fake()->sentence(3),
            'description' => fake()->paragraph(),
            'workload' => 10,
            'file_id' => File::factory()->create()->id,
        ], $overrides));
    }
}
