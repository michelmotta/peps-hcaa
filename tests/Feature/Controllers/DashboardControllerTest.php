<?php

namespace Tests\Feature\Dashboard;

use App\Enums\ProfileEnum;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\User;
use App\Models\File; // Importe o model File
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $coordinator;
    protected User $professor;
    protected User $anotherProfessor;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);
        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->coordinator = $this->createUser();
        $this->coordinator->profiles()->attach(ProfileEnum::COORDENADOR->value);

        $this->professor = $this->createUser();
        $this->professor->profiles()->attach(ProfileEnum::PROFESSOR->value);

        $this->anotherProfessor = $this->createUser();
        $this->anotherProfessor->profiles()->attach(ProfileEnum::PROFESSOR->value);

        $this->student = $this->createUser();

        $myLessons = $this->createCompleteLesson(['user_id' => $this->professor->id], 2);
        $this->createCompleteLesson(['user_id' => $this->anotherProfessor->id]);

        $student1 = $this->createUser();
        $student2 = $this->createUser();

        $myLessons[0]->subscriptions()->attach($student1);
        $myLessons[1]->subscriptions()->attach($student2);
    }

    private function createUser(array $overrides = []): User
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

    private function createCompleteLesson(array $overrides = [], int $count = 1)
    {
        $defaults = [
            'name' => 'Aula de Teste: ' . fake()->sentence(3),
            'description' => fake()->paragraph(),
            'workload' => 10,
            'user_id' => $this->professor->id,
            'file_id' => File::factory()->create()->id,
        ];

        return Lesson::factory($count)->create(array_merge($defaults, $overrides));
    }

    #[Test]
    public function coordenador_ve_contagens_totais_de_aulas_e_alunos(): void
    {
        $response = $this->actingAs($this->coordinator)->get(route('dashboard.index'));

        $response->assertOk();
        $response->assertViewIs('dashboard.index');
        $response->assertViewHas('classesCount', 3);
        $response->assertViewHas('studentsCount', 2);
    }

    #[Test]
    public function professor_ve_contagens_apenas_de_suas_proprias_aulas_e_alunos(): void
    {
        $response = $this->actingAs($this->professor)->get(route('dashboard.index'));

        $response->assertOk();
        $response->assertViewIs('dashboard.index');
        $response->assertViewHas('classesCount', 2);
        $response->assertViewHas('studentsCount', 2);
    }

    #[Test]
    public function usuario_padrao_estudante_nao_pode_acessar_o_dashboard(): void
    {
        $response = $this->actingAs($this->student)->get(route('dashboard.index'));
        $response->assertForbidden();
    }
}
