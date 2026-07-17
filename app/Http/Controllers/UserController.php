<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Listar todos os usuários com paginação de 10 por página
    public function index()
    {
        // Garante que APENAS admin e bibliotecario possam ver a lista. Clientes são barrados.
        if (auth()->user()?->role === 'cliente') {
            abort(403, 'Acesso não autorizado.');
        }

        $users = User::paginate(10); // Paginação para 10 usuários por página 
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
        // Bloqueia o acesso se não for o Admin
    $this->authorize('update', $user);

    return view('users.edit', compact('user'));
    }

    // Salvar as alterações do usuário no banco de dados
    public function update(Request $request, User $user)
    {
        // Bloqueia a requisição de salvamento se não for o Admin
        $this->authorize('update', $user);
        
        // Validação recomendada para evitar dados nulos ou e-mails duplicados
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        // Atualiza apenas os campos permitidos pela atividade 
        $user->update($request->only('name', 'email')); 

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.'); // [cite: 337, 338]
    }

    public function clearDebit(User $user)
    {
        // Segurança: Apenas quem não é cliente pode zerar multas
        if (auth()->user()->role === 'cliente') {
            abort(403, 'Acesso não autorizado.');
        }

        // Zera o valor do débito do usuário
        $user->update(['debit' => 0.00]);

        return redirect()->back()->with('success', "O débito do usuário {$user->name} foi zerado com sucesso!");
    }
}