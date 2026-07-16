<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    // O Admin faz absolutamente tudo automaticamente
    public function before(User $user, $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return true; // Qualquer logado visualiza
    }

    public function view(User $user, Category $category): bool
    {
        return true; 
    }

    public function create(User $user): bool
    {
        return $user->role === 'bibliotecario';
    }

    public function update(User $user, Category $category): bool
    {
        return $user->role === 'bibliotecario';
    }

    public function delete(User $user, Category $category): bool
    {
        return false; // Apenas o admin deleta (e o before() já cuida disso)
    }
}