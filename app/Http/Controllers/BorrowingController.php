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
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        Borrowing::create([
            'user_id' => $request->user_id,
            'book_id' => $book->id,
            'borrowed_at' => now(),
        ]);

        return redirect()->route('books.show', $book)->with('success', 'Empréstimo registrado com sucesso.');
    }

    public function returnBook($id)
    {
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
        $borrowings = $user->books()->withPivot('borrowed_at', 'returned_at')->get();

        return view('users.borrowings', compact('user', 'borrowings'));
    }
}