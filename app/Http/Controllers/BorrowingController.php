<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Support\Facades\DB;

class BorrowingController extends Controller
{
    public function store(Request $request, Book $book)
    {
        // 1. Segurança: Apenas admin ou bibliotecário podem registrar empréstimos
        if (auth()->user()->role === 'cliente') {
            abort(403, 'Acesso não autorizado.');
        }

        // [ATIVIDADE 8] - Validação solicitada pelo professor Alexandre:
        // Verifica se já existe um empréstimo em aberto (returned_at é nulo) para ESTE livro na tabela borrowings
        $temEmprestimoEmAberto = Borrowing::where('book_id', $book->id)
                                          ->whereNull('returned_at')
                                          ->exists();

        if ($temEmprestimoEmAberto) {
            // Se o livro já estiver com alguém, bloqueia e redireciona de volta com erro
            return redirect()->back()->with('error', 'Este livro já possui um empréstimo em aberto e não pode ser emprestado novamente!');
        }

        // Validação padrão do formulário
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Cria o registro do empréstimo na tabela borrowings
        Borrowing::create([
            'user_id' => $request->user_id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Empréstimo registrado com sucesso.');
    }

    public function returnBook($id)
    {
        // 1. Segurança: Apenas admin ou bibliotecário podem receber devoluções
        if (auth()->user()->role === 'cliente') {
            abort(403, 'Acesso não autorizado.');
        }

        // 2. Atualiza a coluna returned_at na tabela borrowings
        $updated = DB::table('borrowings')
            ->where('id', $id)
            ->update([
                'returned_at' => now(),
                'updated_at' => now()
            ]);

        if ($updated) {
            return redirect()->back()->with('success', 'Livro devolvido com sucesso!');
        }

        return redirect()->back()->with('error', 'Não foi possível registrar a devolução.');
    }
    
    public function userBorrowings(User $user)
    {
        $borrowings = $user->books()->withPivot('id', 'borrowed_at', 'returned_at')->get();

        return view('users.borrowings', compact('user', 'borrowings'));
    }
}