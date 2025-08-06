<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function isCoordenador(User $user): bool
    {
        return $user->hasProfile('Coordenador');
    }

    public function isProfessor(User $user): bool
    {
        return $user->hasProfile('Professor');
    }

    public function isCoordenadorOrProfessor(User $user): bool
    {
        return $user->hasAnyProfile(['Coordenador', 'Professor']);
    }

    public function isOnlyProfessor(User $user): bool
    {
        return $user->hasOnlyProfessorProfile();
    }
}
