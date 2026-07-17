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
            // Busca o usuário que está tentando pegar o livro
        $user = User::findOrFail($request->user_id);

        // 2. VERIFICA SE O USUÁRIO POSSUI DÉBITO
        if ($user->debit > 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Empréstimo não permitido: este usuário possui débitos pendentes de multas!');
        }
        
        if (auth()->user()->role === 'cliente') {
            abort(403, 'Acesso não autorizado.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // [ATIVIDADE 10] - Bloquear usuários com débitos pendentes
        $usuario = User::find($request->user_id);
        if ($usuario->debit > 0) {
            return redirect()->back()->with('error', "O usuário {$usuario->name} possui um débito pendente de R$ " . number_format($usuario->debit, 2, ',', '.') . " e não pode realizar novos empréstimos!");
        }

        // [ATIVIDADE 8] - Verifica se o LIVRO já está emprestado
        $temEmprestimoEmAberto = Borrowing::where('book_id', $book->id)
                                          ->whereNull('returned_at')
                                          ->exists();

        if ($temEmprestimoEmAberto) {
            return redirect()->back()->with('error', 'Este livro já possui um empréstimo em aberto e não pode ser emprestado novamente!');
        }

        // [ATIVIDADE 9] - Limite de 5 empréstimos por usuário
        $quantidadeEmprestimosAtivos = Borrowing::where('user_id', $request->user_id)
                                                ->whereNull('returned_at')
                                                ->count();

        if ($quantidadeEmprestimosAtivos >= 5) {
            return redirect()->back()->with('error', 'Este usuário já possui o limite máximo de 5 livros emprestados simultaneamente!');
        }

        Borrowing::create([
            'user_id' => $request->user_id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Empréstimo registrado com sucesso.');
    }

    public function returnBook($id)
    {
        // Encontra o empréstimo
        $borrowing = Borrowing::findOrFail($id);
        
        // Registra a data de devolução atual
        $borrowing->returned_at = now();
        $borrowing->save();

        // 1. CALCULA A MULTA SE HOUVER ATRASO
        $dataEmprestimo = \Carbon\Carbon::parse($borrowing->borrowed_at);
        $diasComLivro = $dataEmprestimo->diffInDays(now());

        if ($diasComLivro > 15) {
            $diasAtraso = $diasComLivro - 15;
            $valorMulta = $diasAtraso * 0.50;

            // Atualiza o débito do usuário acumulando o valor
            $user = $borrowing->user;
            $user->debit += $valorMulta;
            $user->save();

            return redirect()->back()->with('success', "Livro devolvido! Atraso de {$diasAtraso} dias. Multa de R$ " . number_format($valorMulta, 2, ',', '.') . " adicionada ao usuário.");
        }

        return redirect()->back()->with('success', 'Livro devolvido no prazo com sucesso!');
    }
    
    public function userBorrowings(User $user)
    {
        $borrowings = $user->books()->withPivot('id', 'borrowed_at', 'returned_at')->get();

        return view('users.borrowings', compact('user', 'borrowings'));
    }
}