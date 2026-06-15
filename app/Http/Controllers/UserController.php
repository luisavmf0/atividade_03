<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Listar todos os usuários com paginação de 10 por página
    public function index()
    {
        $users = User::paginate(10); // Paginação para 10 usuários por página [cite: 324]
        return view('users.index', compact('users')); // [cite: 324]
    }

    // Mostrar os detalhes de um usuário específico
    public function show(User $user)
    {
        return view('users.show', compact('user')); // [cite: 328]
    }

    // Exibir o formulário de edição do usuário
    public function edit(User $user)
    {
        return view('users.edit', compact('user')); // [cite: 332]
    }

    // Salvar as alterações do usuário no banco de dados
    public function update(Request $request, User $user)
    {
        // Validação recomendada para evitar dados nulos ou e-mails duplicados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        // Atualiza apenas os campos permitidos pela atividade 
        $user->update($request->only('name', 'email')); // 

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.'); // [cite: 337, 338]
    }
}