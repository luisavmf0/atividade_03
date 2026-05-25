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
            <p><strong>Status atual:</strong>
                @php
                    // Verifica se há algum empréstimo ativo para este livro
                    $isBorrowed = DB::table('borrowings')->where('book_id', $book->id)->whereNull('returned_at')->exists();
                @endphp

                @if($isBorrowed)
                    <span class="badge bg-danger">Emprestado actualmente</span>
                @else
                    <span class="badge bg-success">Disponível</span>
                @endif
            </p>
        </div>
    </div> @if(!$isBorrowed)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">Registrar Empréstimo deste Livro</div>
        <div class="card-body">
            <form action="{{ route('books.borrow', $book) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="user_id" class="form-label">Selecione o Usuário que vai pegar o livro:</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        <option value="">-- Escolha um usuário --</option>
                        @foreach(\App\Models\User::all() as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Confirmar Empréstimo</button>
            </form>
        </div>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">Histórico de Empréstimos deste Livro</div>
        <div class="card-body">
            @if($book->users->isEmpty())
                <p class="text-muted mb-0">Este livro nunca foi emprestado.</p>
            @else
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($book->users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($user->pivot->borrowed_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($user->pivot->returned_at)
                                    <span class="badge bg-success">Devolvido em {{ \Carbon\Carbon::parse($user->pivot->returned_at)->format('d/m/Y H:i') }}</span>
                                @else
                                    <span class="badge bg-warning text-dark">Em Aberto</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <a href="{{ route('books.index') }}" class="btn btn-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar para Lista de Livros
    </a>
</div>
@endsection