<?php

use App\Http\Controllers\BooksControllerApi;
use Illuminate\Support\Facades\Route;

// Rota simplificada da API para o recurso de livros
Route::apiResource('books', BooksControllerApi::class);