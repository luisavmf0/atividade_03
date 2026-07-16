<?php

namespace App\Policies;

use App\Models\Publisher;
use App\Models\User;

class PublisherPolicy
{
    // Admin tem acesso total de forma implícita
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

    public function view(User $user, Publisher $publisher): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'bibliotecario';
    }

    public function update(User $user, Publisher $publisher): bool
    {
        return $user->role === 'bibliotecario';
    }

    public function delete(User $user, Publisher $publisher): bool
    {
        return false; // Delegado apenas ao 'before' do Admin
    }
}