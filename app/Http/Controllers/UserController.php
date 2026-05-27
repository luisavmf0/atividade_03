<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
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