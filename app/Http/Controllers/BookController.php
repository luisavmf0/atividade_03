<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class BookController extends Controller
{
    // Formulário com input de ID
    public function createWithId()
    {
        $this->authorize('create', Book::class); // Impede acesso à tela de criação
        return view('books.create-id');
    }

    // Salvar livro com input de ID
    public function storeWithId(Request $request)
    {
        // Correção das strings de validação (adicionados os caracteres '|' e removidos espaços incorretos)
        $this->authorize('create', Book::class); // Impede que salvem dados no banco
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id'    => 'required|exists:authors,id',
            'category_id'  => 'required|exists:categories,id',
            'pages'        => 'required|integer|min:1',
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
    }

    // Formulário com input select
    public function createWithSelect()
    {
        Gate::authorize('create', Book::class);

        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.create-select', compact('publishers', 'authors', 'categories'));
    }

    // Salvar livro com input select
    public function storeWithSelect(Request $request)
    {
        Gate::authorize('create', Book::class);
        // Correção das strings de validação para receber os campos do seu formulário HTML
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id'    => 'required|exists:authors,id',
            'category_id'  => 'required|exists:categories,id',
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = $path;
        }

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Livro criado com sucesso.');
    }
    
    public function edit(Book $book)
    {
        $this->authorize('update', $book);

        $publishers = Publisher::all();
        $authors = Author::all();
        $categories = Category::all();

        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book); // Impede que atualizem os dados
        // 1. A validação precisa acontecer PRIMEIRO e fora de qualquer condição [cite: 48]
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'publisher_id' => 'required|exists:publishers,id',
            'author_id'    => 'required|exists:authors,id',
            'category_id'  => 'required|exists:categories,id',
            'pages'        => 'required|integer|min:1', // Adicionado para suportar o create-id também
            'cover_image'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Se o usuário enviou uma nova imagem de capa [cite: 55]
        if ($request->hasFile('cover_image')) {
            
            // Se o livro já tinha uma capa antiga armazenada, deleta o arquivo físico do disco [cite: 58, 60]
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            // Salva o novo arquivo na pasta 'covers' dentro do disco public [cite: 62]
            $path = $request->file('cover_image')->store('covers', 'public');
            
            // Alimenta o array validado com o novo caminho [cite: 62]
            $validated['cover_image'] = $path;
        }

        // 3. Atualiza os dados no banco de dados usando o array correto [cite: 63]
        $book->update($validated);

        // 4. Redireciona de volta para a listagem principal [cite: 64]
        return redirect()->route('books.index')->with('success', 'Livro atualizado com sucesso.');
    }
    
    public function show(Book $book)
    {
        // Carregando autor, editora e categoria do livro com eager loading
        $book->load(['author', 'publisher', 'category']);

        // [NOVO] Carregar os empréstimos deste livro diretamente pelo Model Borrowing,
        // trazendo os dados do usuário junto (e ordenando pelo mais recente)
        $historicoEmprestimos = \App\Models\Borrowing::with('user')
                                    ->where('book_id', $book->id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        // Carrega todos os usuários para popular o select de novos empréstimos
        $users = \App\Models\User::all();

        // Passamos a nova variável $historicoEmprestimos para a View
        return view('books.show', compact('book', 'users', 'historicoEmprestimos'));
    }
    
    public function index()
    {
        // Carregar os livros com autores usando eager loading e paginação
        $books = Book::with('author')->paginate(20);

        return view('books.index', compact('books'));
    }
    
    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        Gate::authorize('delete', $book);
        
        // Remove a imagem de capa do armazenamento local antes de deletar o registro 
        if ($book->cover_image) { 
            Storage::disk('public')->delete($book->cover_image); 
        }

        $book->delete(); 

        return redirect()->route('books.index')->with('success', '...');
        //return redirect()->route('books.index')->with('success', 'Livro excluído com sucesso. [cite: 73]');
    }
}