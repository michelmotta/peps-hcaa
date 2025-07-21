<?php

namespace App\Policies;

use App\Enums\LessonStatusEnum;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lesson $lesson): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lesson $lesson): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lesson $lesson): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lesson $lesson): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lesson $lesson): bool
    {
        return false;
    }

    public function finishedLesson(User $user, Lesson $lesson): bool
    {
        return $user->subscriptions()
            ->where('lesson_id', $lesson->id)
            ->wherePivot('finished', true)
            ->exists();
    }

    public function canGenerateStudentCertificate(User $user, Lesson $lesson): bool
    {
        return $this->finishedLesson($user, $lesson);
    }

    public function canProfessorAskForPublication(User $user, Lesson $lesson): bool
    {
        return $user->hasOnlyProfessorProfile() && $lesson->lesson_status === LessonStatusEnum::RASCUNHO->value;
    }

    public function canCoordenadorPublish(User $user, Lesson $lesson): bool
    {
        return $user->hasProfile('Coordenador') &&
            in_array($lesson->lesson_status, [
                LessonStatusEnum::RASCUNHO->value,
                LessonStatusEnum::AGUARDANDO_PUBLICACAO->value,
            ]);
    }

    public function canCoordenadorUnpublish(User $user, Lesson $lesson): bool
    {
        return $user->hasProfile('Coordenador') &&
            $lesson->lesson_status === LessonStatusEnum::PUBLICADA->value;
    }

    public function canGenerateTeacherCertificate(User $user, Lesson $lesson, User $teacher): bool
    {
        return $lesson->teacher->id === $teacher->id && $lesson->lesson_status === LessonStatusEnum::PUBLICADA->value;
    }
}
