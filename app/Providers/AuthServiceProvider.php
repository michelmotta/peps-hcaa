<?php

namespace App\Providers;

use App\Models\Lesson;
use App\Models\User;
use App\Policies\LessonPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Lesson::class => LessonPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('isProfessor', [UserPolicy::class, 'isProfessor']);
        Gate::define('isCoordenador', [UserPolicy::class, 'isCoordenador']);
        Gate::define('isCoordenadorOrProfessor', [UserPolicy::class, 'isCoordenadorOrProfessor']);

        Gate::define('finishedLesson', [LessonPolicy::class, 'finishedLesson']);
        Gate::define('canGenerateStudentCertificate', [LessonPolicy::class, 'canGenerateStudentCertificate']);
        Gate::define('canProfessorAskForPublication', [LessonPolicy::class, 'canProfessorAskForPublication']);
        Gate::define('canCoordenadorPublish', [LessonPolicy::class, 'canCoordenadorPublish']);
        Gate::define('canCoordenadorUnpublish', [LessonPolicy::class, 'canCoordenadorUnpublish']);
        Gate::define('canGenerateTeacherCertificate', [LessonPolicy::class, 'canGenerateTeacherCertificate']);
    }
}
