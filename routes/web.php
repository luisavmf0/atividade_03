<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BorrowingController;

Route::get('/', function () {
    return view ('welcome');
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::resource('categories', CategoryController::class);
Route::resource('authors', AuthorController::class);
Route::resource('publishers', PublisherController::class);
// Rotas para criação de livros
Route::get('/books/create-id-number', [BookController::class, 'createWithId'])->name('books.create.id');
Route::post('/books/create-id-number', [BookController::class, 'storeWithId'])->name('books.store.id');

Route::get('/books/create-select', [BookController::class, 'createWithSelect'])->name('books.create.select');
Route::post('/books/create-select', [BookController::class, 'storeWithSelect'])->name('books.store.select');

// Rotas RESTful para index, show, edit, update, delete (tem que ficar depois das rotas /books/create-id-number e /books/create-select)
Route::resource('books', BookController::class)->except(['create', 'store']);
Route::resource('users', UserController::class)->except(['create', 'store', 'destroy']);
Route::post('/books/{book}/borrow', [BorrowingController::class, 'store'])->name('books.borrow');
// Rota para listar o histórico de empréstimos de um usuário
Route::get('/users/{user}/borrowings', [BorrowingController::class, 'userBorrowings'])->name('users.borrowings');
// Rota para registrar a devolução
Route::patch('/borrowings/{id}/return', [BorrowingController::class, 'returnBook'])->name('borrowings.return');
// Apenas o admin pode acessar a rota que edita papéis de usuários

//  AGORA (Permite os dois papéis)
Route::middleware(['auth', 'role:admin,bibliotecario'])->group(function () {
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/clear-debit', [UserController::class, 'clearDebit'])->name('users.clearDebit');
});

// Rota para zerar o débito do usuário (apenas admin/bibliotecario podem acessar)
Route::patch('/users/{user}/clear-debit', [UserController::class, 'clearDebit'])->name('users.clearDebit');