<?php

namespace App\Policies;

use App\Models\Author;
use App\Models\User;

class AuthorPolicy
{
    // O Administrador passa direto por qualquer verificação
    public function before(User $user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Author $author): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'bibliotecario';
    }

    public function update(User $user, Author $author): bool
    {
        return $user->role === 'bibliotecario';
    }

    public function delete(User $user, Author $author): bool
    {
        return false; // Apenas o admin deleta (tratado no before)
    }
}