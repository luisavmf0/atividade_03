@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Detalhes do Livro</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Título:</strong> {{ $book->title }}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center mb-3 mb-md-0">
                    <img src="{{ $book->cover_image ? asset('storage/' . $book->cover_image) : asset('images/default-cover.png') }}" 
                         alt="Capa do Livro" 
                         class="img-fluid rounded shadow-sm" 
                         style="max-height: 300px; object-fit: cover;">
                </div>

                <div class="col-md-9 d-flex flex-column justify-content-center">
                    <p><strong>Autor:</strong>
                        <a href="{{ route('authors.show', $book->author->id) }}">
                            {{ $book->author->name }}
                        </a>
                    </p>
                    <p><strong>Editora:</strong>
                        <a href="{{ route('publishers.show', $book->publisher->id) }}">
                            {{ $book->publisher->name }}
                        </a>
                    </p>
                    <p><strong>Categoria:</strong>
                        <a href="{{ route('categories.show', $book->category->id) }}">
                            {{ $book->category->name }}
                        </a>
                    </p>
                </div>
            </div>

        </div>
    </div>

    <a href="{{ route('books.index') }}" class="btn btn-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar para Lista de Livros
    </a>
</div>
@endsection