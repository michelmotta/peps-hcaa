<?php

namespace Tests\Unit\Policies;

use App\Enums\LessonStatusEnum;
use App\Enums\ProfileEnum;
use App\Models\Lesson;
use App\Models\Profile;
use App\Models\User;
use App\Policies\LessonPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LessonPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $coordenador;
    protected User $professor;
    protected User $outroProfessor;
    protected User $aluno;
    protected Lesson $aula;

    protected function setUp(): void
    {
        parent::setUp();

        Profile::forceCreate(['id' => ProfileEnum::COORDENADOR->value, 'name' => 'Coordenador']);
        Profile::forceCreate(['id' => ProfileEnum::PROFESSOR->value, 'name' => 'Professor']);

        $this->coordenador = User::factory()->create();
        $this->coordenador->profiles()->attach(ProfileEnum::COORDENADOR->value);
        $this->coordenador->load('profiles');

        $this->professor = User::factory()->create();
        $this->professor->profiles()->attach(ProfileEnum::PROFESSOR->value);
        $this->professor->load('profiles');

        $this->outroProfessor = User::factory()->create();
        $this->outroProfessor->profiles()->attach(ProfileEnum::PROFESSOR->value);
        $this->outroProfessor->load('profiles');

        $this->aluno = User::factory()->create();

        $this->aula = Lesson::factory()->create(['user_id' => $this->professor->id]);
        $this->aula->load('teacher');
    }

    #[Test]
    public function finishedLesson_retorna_true_quando_aluno_concluiu_aula(): void
    {
        $this->aluno->subscriptions()->attach($this->aula->id, ['finished' => true]);

        $policy = new LessonPolicy();

        $this->assertTrue($policy->finishedLesson($this->aluno, $this->aula));
    }

    #[Test]
    public function finishedLesson_retorna_false_quando_aluno_nao_concluiu_aula(): void
    {
        $this->aluno->subscriptions()->attach($this->aula->id, ['finished' => false]);

        $policy = new LessonPolicy();

        $this->assertFalse($policy->finishedLesson($this->aluno, $this->aula));
    }

    #[Test]
    public function finishedLesson_retorna_false_quando_aluno_nao_tem_inscricao(): void
    {
        $policy = new LessonPolicy();

        $this->assertFalse($policy->finishedLesson($this->aluno, $this->aula));
    }

    #[Test]
    public function canGenerateStudentCertificate_retorna_true_quando_aluno_concluiu_aula(): void
    {
        $this->aluno->subscriptions()->attach($this->aula->id, ['finished' => true]);
        $policy = new LessonPolicy();

        $this->assertTrue($policy->canGenerateStudentCertificate($this->aluno, $this->aula));
    }

    #[Test]
    public function canGenerateStudentCertificate_retorna_false_quando_aluno_nao_concluiu_aula(): void
    {
        $this->aluno->subscriptions()->attach($this->aula->id, ['finished' => false]);
        $policy = new LessonPolicy();

        $this->assertFalse($policy->canGenerateStudentCertificate($this->aluno, $this->aula));
    }

    #[Test]
    public function canProfessorAskForPublication_retorna_true_para_professor_com_aula_rascunho(): void
    {
        $this->professor->profiles()->sync([ProfileEnum::PROFESSOR->value]);
        $this->aula->lesson_status = LessonStatusEnum::RASCUNHO->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertTrue($policy->canProfessorAskForPublication($this->professor, $this->aula));
    }

    #[Test]
    public function canProfessorAskForPublication_retorna_false_para_professor_com_aula_publicada(): void
    {
        $this->professor->profiles()->sync([ProfileEnum::PROFESSOR->value]);
        $this->aula->lesson_status = LessonStatusEnum::PUBLICADA->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertFalse($policy->canProfessorAskForPublication($this->professor, $this->aula));
    }

    #[Test]
    public function canCoordenadorPublish_retorna_true_quando_aula_rascunho(): void
    {
        $this->aula->lesson_status = LessonStatusEnum::RASCUNHO->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertTrue($policy->canCoordenadorPublish($this->coordenador, $this->aula));
    }

    #[Test]
    public function canCoordenadorPublish_retorna_true_quando_aula_aguardando_publicacao(): void
    {
        $this->aula->lesson_status = LessonStatusEnum::AGUARDANDO_PUBLICACAO->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertTrue($policy->canCoordenadorPublish($this->coordenador, $this->aula));
    }

    #[Test]
    public function canCoordenadorPublish_retorna_false_quando_aula_em_status_diferente(): void
    {
        $this->aula->lesson_status = LessonStatusEnum::PUBLICADA->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertFalse($policy->canCoordenadorPublish($this->coordenador, $this->aula));
    }

    #[Test]
    public function canCoordenadorPublish_retorna_false_quando_usuario_nao_e_coordenador(): void
    {
        $this->aula->lesson_status = LessonStatusEnum::RASCUNHO->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertFalse($policy->canCoordenadorPublish($this->professor, $this->aula));
    }

    #[Test]
    public function canCoordenadorUnpublish_retorna_true_quando_aula_publicada(): void
    {
        $this->aula->lesson_status = LessonStatusEnum::PUBLICADA->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertTrue($policy->canCoordenadorUnpublish($this->coordenador, $this->aula));
    }

    #[Test]
    public function canCoordenadorUnpublish_retorna_false_quando_aula_nao_publicada(): void
    {
        $this->aula->lesson_status = LessonStatusEnum::RASCUNHO->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertFalse($policy->canCoordenadorUnpublish($this->coordenador, $this->aula));
    }

    #[Test]
    public function canCoordenadorUnpublish_retorna_false_quando_usuario_nao_e_coordenador(): void
    {
        $this->aula->lesson_status = LessonStatusEnum::PUBLICADA->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertFalse($policy->canCoordenadorUnpublish($this->professor, $this->aula));
    }

    #[Test]
    public function canGenerateTeacherCertificate_retorna_true_quando_professor_correto_e_aula_publicada(): void
    {
        $this->aula->lesson_status = LessonStatusEnum::PUBLICADA->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertTrue($policy->canGenerateTeacherCertificate($this->professor, $this->aula, $this->aula->teacher));
    }

    #[Test]
    public function canGenerateTeacherCertificate_retorna_false_quando_aula_nao_publicada(): void
    {
        $this->aula->lesson_status = LessonStatusEnum::RASCUNHO->value;
        $this->aula->save();

        $policy = new LessonPolicy();

        $this->assertFalse($policy->canGenerateTeacherCertificate($this->professor, $this->aula, $this->aula->teacher));
    }
}
