<?php

use App\Models\Doubt;
use App\Models\Guidebook;
use App\Models\GuidebookCategory;
use App\Models\History;
use App\Models\Information;
use App\Models\Lesson;
use App\Models\Library;
use App\Models\Quiz;
use App\Models\Specialty;
use App\Models\Suggestion;
use App\Models\Topic;
use App\Models\User;
use Tabuna\Breadcrumbs\Breadcrumbs;
use Tabuna\Breadcrumbs\Trail;

// ----------------------------------------------------------------
// HOME
// ----------------------------------------------------------------
Breadcrumbs::for(
    'dashboard.index',
    fn(Trail $trail) =>
    $trail->push('Dashboard', route('dashboard.index'))
);


// ----------------------------------------------------------------
// LESSONS & NESTED RESOURCES
// ----------------------------------------------------------------

// Lessons
Breadcrumbs::for(
    'dashboard.lessons.index',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Aulas', route('dashboard.lessons.index'))
);

Breadcrumbs::for(
    'dashboard.lessons.create',
    fn(Trail $trail) =>
    $trail->parent('dashboard.lessons.index')
        ->push('Nova Aula', route('dashboard.lessons.create'))
);

Breadcrumbs::for(
    'dashboard.lessons.edit',
    fn(Trail $trail, Lesson $lesson) =>
    $trail->parent('dashboard.lessons.index')
        ->push("Editar: {$lesson->name}", route('dashboard.lessons.edit', $lesson))
);

Breadcrumbs::for(
    'dashboard.lessons.show',
    fn(Trail $trail, Lesson $lesson) =>
    $trail->parent('dashboard.lessons.index')
        ->push($lesson->name, route('dashboard.lessons.show', $lesson))
);

// Lesson Topics
Breadcrumbs::for(
    'dashboard.lessons.topics.index',
    fn(Trail $trail, Lesson $lesson) =>
    $trail->parent('dashboard.lessons.show', $lesson)
        ->push('Tópicos', route('dashboard.lessons.topics.index', $lesson))
);

Breadcrumbs::for(
    'dashboard.lessons.topics.create',
    fn(Trail $trail, Lesson $lesson) =>
    $trail->parent('dashboard.lessons.topics.index', $lesson)
        ->push('Novo Tópico', route('dashboard.lessons.topics.create', $lesson))
);

Breadcrumbs::for(
    'dashboard.lessons.topics.edit',
    fn(Trail $trail, Lesson $lesson, Topic $topic) =>
    $trail->parent('dashboard.lessons.topics.index', $lesson)
        ->push("Editar: {$topic->title}", route('dashboard.lessons.topics.edit', [$lesson, $topic]))
);

// Lesson Subscriptions (Students)
Breadcrumbs::for(
    'dashboard.lessons.subscriptions.index',
    fn(Trail $trail, Lesson $lesson) =>
    $trail->parent('dashboard.lessons.show', $lesson)
        ->push('Inscrições', route('dashboard.lessons.subscriptions.index', $lesson))
);

// Lesson Doubts
Breadcrumbs::for(
    'dashboard.lessons.doubts.index',
    fn(Trail $trail, Lesson $lesson) =>
    $trail->parent('dashboard.lessons.show', $lesson)
        ->push('Dúvidas', route('dashboard.lessons.doubts.index', $lesson))
);

Breadcrumbs::for(
    'dashboard.lessons.doubts.create',
    fn(Trail $trail, Lesson $lesson) =>
    $trail->parent('dashboard.lessons.doubts.index', $lesson)
        ->push('Nova Dúvida', route('dashboard.lessons.doubts.create', $lesson))
);

Breadcrumbs::for(
    'dashboard.lessons.doubts.edit',
    fn(Trail $trail, Lesson $lesson, Doubt $doubt) =>
    $trail->parent('dashboard.lessons.doubts.index', $lesson)
        ->push("Responder Dúvida", route('dashboard.lessons.doubts.edit', [$lesson, $doubt]))
);


// ----------------------------------------------------------------
// USERS
// ----------------------------------------------------------------
Breadcrumbs::for(
    'dashboard.users.index',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Usuários', route('dashboard.users.index'))
);

Breadcrumbs::for(
    'dashboard.users.create',
    fn(Trail $trail) =>
    $trail->parent('dashboard.users.index')
        ->push('Novo Usuário', route('dashboard.users.create'))
);

Breadcrumbs::for(
    'dashboard.users.edit',
    fn(Trail $trail, User $user) =>
    $trail->parent('dashboard.users.index')
        ->push("Editar: {$user->name}", route('dashboard.users.edit', $user))
);

Breadcrumbs::for(
    'dashboard.users.show',
    fn(Trail $trail, User $user) =>
    $trail->parent('dashboard.users.index')
        ->push($user->name, route('dashboard.users.show', $user))
);


// ----------------------------------------------------------------
// SPECIALTIES
// ----------------------------------------------------------------
Breadcrumbs::for(
    'dashboard.specialties.index',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Especialidades', route('dashboard.specialties.index'))
);

Breadcrumbs::for(
    'dashboard.specialties.create',
    fn(Trail $trail) =>
    $trail->parent('dashboard.specialties.index')
        ->push('Nova Especialidade', route('dashboard.specialties.create'))
);

Breadcrumbs::for(
    'dashboard.specialties.edit',
    fn(Trail $trail, Specialty $specialty) =>
    $trail->parent('dashboard.specialties.index')
        ->push("Editar: {$specialty->name}", route('dashboard.specialties.edit', $specialty))
);


// ----------------------------------------------------------------
// SUGGESTIONS
// ----------------------------------------------------------------
Breadcrumbs::for(
    'dashboard.suggestions.index',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Sugestões', route('dashboard.suggestions.index'))
);

Breadcrumbs::for(
    'dashboard.suggestions.create',
    fn(Trail $trail) =>
    $trail->parent('dashboard.suggestions.index')
        ->push('Nova Sugestão', route('dashboard.suggestions.create'))
);

Breadcrumbs::for(
    'dashboard.suggestions.edit',
    fn(Trail $trail, Suggestion $suggestion) =>
    $trail->parent('dashboard.suggestions.index')
        ->push('Editar Sugestão', route('dashboard.suggestions.edit', $suggestion))
);


// ----------------------------------------------------------------
// GUIDEBOOKS & CATEGORIES
// ----------------------------------------------------------------

// Guidebooks
Breadcrumbs::for(
    'dashboard.guidebooks.index',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Guias Práticos', route('dashboard.guidebooks.index'))
);

Breadcrumbs::for(
    'dashboard.guidebooks.create',
    fn(Trail $trail) =>
    $trail->parent('dashboard.guidebooks.index')
        ->push('Novo Guia', route('dashboard.guidebooks.create'))
);

Breadcrumbs::for(
    'dashboard.guidebooks.edit',
    fn(Trail $trail, Guidebook $guidebook) =>
    $trail->parent('dashboard.guidebooks.index')
        ->push("Editar: {$guidebook->name}", route('dashboard.guidebooks.edit', $guidebook))
);

// Guidebook Categories
Breadcrumbs::for(
    'dashboard.guidebook-categories.index',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Categorias de Guias', route('dashboard.guidebook-categories.index'))
);

Breadcrumbs::for(
    'dashboard.guidebook-categories.create',
    fn(Trail $trail) =>
    $trail->parent('dashboard.guidebook-categories.index')
        ->push('Nova Categoria', route('dashboard.guidebook-categories.create'))
);

Breadcrumbs::for(
    'dashboard.guidebook-categories.edit',
    fn(Trail $trail, GuidebookCategory $guidebookCategory) =>
    $trail->parent('dashboard.guidebook-categories.index')
        ->push("Editar: {$guidebookCategory->name}", route('dashboard.guidebook-categories.edit', $guidebookCategory))
);


// ----------------------------------------------------------------
// OTHER RESOURCES
// ----------------------------------------------------------------

// Quizzes
Breadcrumbs::for(
    'dashboard.quizzes.index',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Quizzes', route('dashboard.quizzes.index'))
);

// Histories
Breadcrumbs::for(
    'dashboard.histories.index',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Históricos', route('dashboard.histories.index'))
);

// Library
Breadcrumbs::for(
    'dashboard.libraries.index',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Biblioteca', route('dashboard.libraries.index'))
);


// ----------------------------------------------------------------
// REPORTS
// ----------------------------------------------------------------
Breadcrumbs::for(
    'dashboard.reports',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Relatórios')
);

Breadcrumbs::for(
    'dashboard.reports.students',
    fn(Trail $trail) =>
    $trail->parent('dashboard.reports')
        ->push('Relatório de Alunos', route('dashboard.reports.students'))
);

Breadcrumbs::for(
    'dashboard.reports.teachers',
    fn(Trail $trail) =>
    $trail->parent('dashboard.reports')
        ->push('Relatório de Professores', route('dashboard.reports.teachers'))
);

Breadcrumbs::for(
    'dashboard.reports.lessons',
    fn(Trail $trail) =>
    $trail->parent('dashboard.reports')
        ->push('Relatório de Aulas', route('dashboard.reports.lessons'))
);
