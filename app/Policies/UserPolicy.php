<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * O método 'before' garante que se o usuário logado for admin,
     * ele tem permissão total automaticamente.
     */
    public function before(User $user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * Qualquer outra role (bibliotecario ou cliente) terá o acesso negado por padrão.
     */
    public function viewAny(User $user): bool
    {
        return false; 
    }

    public function update(User $user, User $model): bool
    {
        return false; 
    }
}