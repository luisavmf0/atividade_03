<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BooksControllerApi extends Controller
{
    /**
     * GET /api/books
     * Retorna a lista de todos os livros em formato JSON.
     */
    public function index()
    {
        // Traz os livros junto com as relações para o JSON ficar completo
        $books = Book::with(['category', 'author', 'publisher'])->get();
        return response()->json($books, 200);
    }

    /**
     * POST /api/books
     * Cadastra um novo livro no sistema via API.
     */
    public function store(Request $request)
    {
        // Validação básica dos dados obrigatórios
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'author_id' => 'required|integer',
            'publisher_id' => 'required|integer',
        ]);

        $book = Book::create($validated);

        return response()->json([
            'message' => 'Livro criado com sucesso via API!',
            'book' => $book
        ], 201); // 201 significa "Created"
    }

    /**
     * GET /api/books/{id}
     * Retorna os detalhes de um único livro específico.
     */
    public function show($id)
    {
        $book = Book::with(['category', 'author', 'publisher'])->find($id);

        if (!$book) {
            return response()->json(['message' => 'Livro não encontrado.'], 404);
        }

        return response()->json($book, 200);
    }

    /**
     * PUT/PATCH /api/books/{id}
     * Atualiza os dados de um livro existente.
     */
    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Livro não encontrado para atualização.'], 404);
        }

        // Valida apenas o que for enviado
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|integer',
            'author_id' => 'sometimes|integer',
            'publisher_id' => 'sometimes|integer',
        ]);

        $book->update($validated);

        return response()->json([
            'message' => 'Livro atualizado com sucesso via API!',
            'book' => $book
        ], 200);
    }

    /**
     * DELETE /api/books/{id}
     * Remove um livro do banco de dados.
     */
    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json(['message' => 'Livro não encontrado.'], 404);
        }

        $book->delete();

        return response()->json(['message' => 'Livro excluído com sucesso via API!'], 200);
    }
}