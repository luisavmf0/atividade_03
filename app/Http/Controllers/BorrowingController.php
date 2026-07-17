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

        // Validação padrão do formulário (precisa ser feita primeiro para garantir que temos o 'user_id' válido)
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // [ATIVIDADE 8] - Verifica se o LIVRO já está emprestado para alguém
        $temEmprestimoEmAberto = Borrowing::where('book_id', $book->id)
                                          ->whereNull('returned_at')
                                          ->exists();

        if ($temEmprestimoEmAberto) {
            return redirect()->back()->with('error', 'Este livro já possui um empréstimo em aberto e não pode ser emprestado novamente!');
        }

        // [ATIVIDADE 9] - Validação de limite de empréstimos por usuário:
        // Conta quantos empréstimos ativos (returned_at é nulo) o leitor selecionado já possui
        $quantidadeEmprestimosAtivos = Borrowing::where('user_id', $request->user_id)
                                                ->whereNull('returned_at')
                                                ->count();

        if ($quantidadeEmprestimosAtivos >= 5) {
            // Se já tiver 5 ou mais livros com ele, bloqueia o novo empréstimo
            return redirect()->back()->with('error', 'Este usuário já possui o limite máximo de 5 livros emprestados simultaneamente!');
        }

        // Se passar em todas as validações, cria o registro do empréstimo na tabela borrowings
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