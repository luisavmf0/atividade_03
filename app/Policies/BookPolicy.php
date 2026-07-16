<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\Response;

    class BookPolicy
    {
        // O admin pode fazer absolutamente tudo
        public function before(User $user, $ability)
        {
            if ($user->role === 'admin') {
                return true;
            }
        }

        // Visualizar: qualquer usuário autenticado (admin, bibliotecario, cliente) pode ver
        public function viewAny(User $user)
        {
            return true; 
        }

        public function view(User $user, Book $book)
        {
            return true;
        }

        // Criar: apenas admin (já liberado no before) e bibliotecario
        public function create(User $user)
        {
            return $user->role === 'bibliotecario';
        }

        // Editar/Atualizar: apenas admin e bibliotecario
        public function update(User $user, Book $book)
        {
            return $user->role === 'bibliotecario';
        }

        // Excluir: a atividade diz que o bibliotecário cria, edita e visualiza. 
        // Se deletar for exclusivo do admin, retornamos false aqui (o 'before' vai garantir o acesso do admin)
        public function delete(User $user, Book $book)
        {
            return false; 
        }
    }