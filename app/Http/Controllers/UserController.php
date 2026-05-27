<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
<<<<<<< HEAD
    {
        // Listar todos os usuários com paginação de 10 por página
        public function index()
        {
            $users = User::paginate(10);
            return view('users.index', compact('users'));
        }

        // Mostrar os detalhes de um usuário específico
        public function show(User $user)
        {
            return view('users.show', compact('user'));
        }

        // Exibir o formulário de edição do usuário
        public function edit(User $user)
        {
            return view('users.edit', compact('user'));
        }

        // Salvar as alterações do usuário no banco de dados
        public function update(Request $request, User $user)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            ]);

            $user->update($request->all());

            return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
        }
    }
=======
{
    public function index()
    {
        $users = \App\Models\User::paginate(10); // Paginação para 10 usuários por página
        return view('users.index', compact('users'));
    }

    public function show(\App\Models\User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(\App\Models\User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, \App\Models\User $user)
    {
        $user->update($request->only('name', 'email'));

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

}
>>>>>>> 42f661f372a20e67531b87b2d98bedab3fa1e479
