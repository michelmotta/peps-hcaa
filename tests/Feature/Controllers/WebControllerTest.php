<?php

namespace Tests\Feature\Web;

use App\Enums\LessonStatusEnum;
use App\Enums\ProfileEnum;
use App\Models\Certificate;
use App\Models\File;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $professor;
    protected User $student;
    protected Lesson $publishedLesson;

    /**
     * Prepara o ambiente para cada teste.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Cria os perfis necessários
        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);
        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);

        // Cria usuários base
        $this->professor = $this->createCompleteUser(['email' => 'professor@teste.com']);
        $this->professor->profiles()->attach(ProfileEnum::PROFESSOR->value);

        $this->student = $this->createCompleteUser(['email' => 'estudante@teste.com']);

        // Cria uma aula publicada para ser usada nos testes
        $this->publishedLesson = $this->createCompleteLesson([
            'user_id' => $this->professor->id,
            'lesson_status' => LessonStatusEnum::PUBLICADA->value,
        ]);
    }

    #[Test]
    public function a_pagina_inicial_carrega_corretamente_para_visitantes_e_usuarios_logados(): void
    {
        // Teste para visitante
        $responseGuest = $this->get(route('web.index'));
        $responseGuest->assertOk();
        $responseGuest->assertViewIs('web.index');
        $responseGuest->assertViewHas('lessons');

        // Teste para usuário logado
        $responseAuth = $this->actingAs($this->student)->get(route('web.index'));
        $responseAuth->assertOk();
    }

    #[Test]
    public function a_pagina_de_uma_aula_carrega_corretamente_para_visitantes_e_usuarios_logados(): void
    {
        // Teste para visitante
        $responseGuest = $this->get(route('web.class', $this->publishedLesson));
        $responseGuest->assertOk();
        $responseGuest->assertViewIs('web.class');
        $responseGuest->assertViewHas('lesson', $this->publishedLesson);

        // Teste para usuário logado
        $responseAuth = $this->actingAs($this->student)->get(route('web.class', $this->publishedLesson));
        $responseAuth->assertOk();
    }

    #[Test]
    public function a_pagina_de_professores_carrega_e_filtra_corretamente(): void
    {
        $response = $this->get(route('web.teachers'));
        $response->assertOk();
        $response->assertViewIs('web.teachers');
        $response->assertViewHas('teachers');

        // Testa a busca (Scout precisa estar configurado com driver 'collection' ou 'null' em phpunit.xml)
        $responseSearch = $this->get(route('web.teachers', ['q' => $this->professor->name]));
        $responseSearch->assertOk();
    }

    #[Test]
    public function a_pagina_de_perfil_de_um_professor_carrega_corretamente_e_redireciona_se_nao_for_professor(): void
    {
        $response = $this->get(route('web.teacher', $this->professor));
        $response->assertOk();
        $response->assertViewIs('web.teacher');
        $response->assertViewHas('teacher', $this->professor);

        // Testa o redirecionamento para um usuário que não é professor
        $responseRedirect = $this->get(route('web.teacher', $this->student));
        $responseRedirect->assertRedirect();
    }

    #[Test]
    public function as_paginas_de_informacoes_biblioteca_e_sugestoes_carregam_corretamente(): void
    {
        $this->get(route('web.informations'))->assertOk();
        $this->get(route('web.library'))->assertOk();
        $this->get(route('web.suggestions'))->assertOk();
    }

    #[Test]
    public function a_pagina_de_aulas_filtra_corretamente(): void
    {
        $response = $this->get(route('web.classes', ['sort_by' => 'oldest']));
        $response->assertOk();
        $response->assertViewIs('web.classes');
    }

    #[Test]
    public function a_pagina_minhas_aulas_requer_autenticacao_e_filtra_corretamente(): void
    {
        $this->get(route('web.myClasses'))->assertRedirect(route('login'));

        $this->student->subscriptions()->attach($this->publishedLesson->id);
        $response = $this->actingAs($this->student)->get(route('web.myClasses', ['sort_by' => 'oldest']));
        $response->assertOk();
        $response->assertViewIs('web.my_classes');
        $response->assertSee($this->publishedLesson->name);
    }

    #[Test]
    public function um_usuario_pode_criar_e_votar_em_sugestoes(): void
    {
        $suggestionData = ['name' => 'Nova Sugestão', 'description' => 'Descrição da sugestão.'];
        $responseCreate = $this->actingAs($this->student)->post(route('web.suggestion-create'), $suggestionData);
        $responseCreate->assertRedirect(route('web.suggestions'));
        $responseCreate->assertSessionHas('success');
        $this->assertDatabaseHas('suggestions', ['name' => 'Nova Sugestão']);

        $suggestion = Suggestion::first();
        $responseUpdate = $this->actingAs($this->student)->patch(route('web.suggestion-update', $suggestion));
        $responseUpdate->assertRedirect(route('web.suggestions'));
        $this->assertEquals(2, $suggestion->fresh()->votes); // Voto inicial + 1
    }

    #[Test]
    public function as_paginas_de_login_perfil_e_termos_carregam_corretamente(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('web.perfil'))->assertOk();
        $this->actingAs($this->student)->get(route('web.perfil'))->assertOk();
        $this->get(route('web.user.terms'))->assertOk();
    }

    #[Test]
    public function um_aluno_pode_gerar_certificado_de_aula_concluida(): void
    {
        $this->student->subscriptions()->attach($this->publishedLesson->id, ['finished' => true, 'finished_at' => now()]);
        $response = $this->actingAs($this->student)->get(route('web.certificates.generate', $this->publishedLesson));
        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    #[Test]
    public function um_aluno_nao_pode_gerar_certificado_de_aula_nao_concluida(): void
    {
        $this->student->subscriptions()->attach($this->publishedLesson->id, ['finished' => false]);
        $response = $this->actingAs($this->student)->get(route('web.certificates.generate', $this->publishedLesson));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[Test]
    public function a_validacao_de_certificado_funciona_corretamente(): void
    {
        $certificate = Certificate::factory()->create(['user_id' => $this->student->id, 'lesson_id' => $this->publishedLesson->id]);

        // Teste GET
        $this->get(route('web.validate.certificate', ['uuid' => $certificate->uuid]))->assertOk();

        // Teste POST com UUID válido
        $responsePostValid = $this->post(route('web.validate.certificate'), ['uuid' => $certificate->uuid]);
        $responsePostValid->assertOk();
        $responsePostValid->assertViewHas('certificate');
        $responsePostValid->assertSeeText($this->student->name);

        // Teste POST com UUID inválido
        $responsePostInvalid = $this->post(route('web.validate.certificate'), ['uuid' => 'uuid-invalido']);
        $responsePostInvalid->assertRedirect();
        $responsePostInvalid->assertSessionHasErrors('uuid');
    }

    /**
     * Função auxiliar para criar um usuário com todos os dados obrigatórios.
     */
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

    /**
     * Função auxiliar para criar uma aula com todos os dados obrigatórios.
     */
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
