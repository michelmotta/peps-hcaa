<?php

use Tabuna\Breadcrumbs\Trail;
use Illuminate\Support\Facades\Route;
use App\Models\Lesson;
use App\Models\Topic;
use App\Models\User;
use App\Models\Suggestion;
use App\Models\Information;
use App\Models\Specialty;
use Tabuna\Breadcrumbs\Breadcrumbs;

// DASHBOARD INICIAL
Breadcrumbs::for(
    'dashboard.index',
    fn(Trail $trail) =>
    $trail->push('Dashboard', route('dashboard.index'))
);

// LESSONS
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

// TOPICS
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

// STUDENTS
Breadcrumbs::for(
    'dashboard.lessons.students.index',
    fn(Trail $trail, Lesson $lesson) =>
    $trail->parent('dashboard.lessons.show', $lesson)
        ->push('Inscrições', route('dashboard.lessons.students.index', $lesson))
);

// SUGGESTIONS
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

// INFORMATION
Breadcrumbs::for(
    'dashboard.information.index',
    fn(Trail $trail) =>
    $trail->parent('dashboard.index')
        ->push('Informações', route('dashboard.information.index'))
);

Breadcrumbs::for(
    'dashboard.information.create',
    fn(Trail $trail) =>
    $trail->parent('dashboard.information.index')
        ->push('Nova Informação', route('dashboard.information.create'))
);

Breadcrumbs::for(
    'dashboard.information.edit',
    fn(Trail $trail, Information $information) =>
    $trail->parent('dashboard.information.index')
        ->push('Editar Informação', route('dashboard.information.edit', $information))
);

// SPECIALTIES
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
        ->push('Editar Especialidade', route('dashboard.specialties.edit', $specialty))
);

// USERS
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
