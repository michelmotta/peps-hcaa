<?php

namespace App\Policies;

use App\Enums\LessonStatusEnum;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LessonPolicy
{
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
