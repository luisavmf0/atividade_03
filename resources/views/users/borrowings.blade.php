@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Histórico de Empréstimos de: {{ $user->name }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($borrowings->isEmpty())
        <div class="alert alert-info">Este usuário não possui nenhum empréstimo registrado.</div>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Livro</th>
                    <th>Data de Empréstimo</th>
                    <th>Data de Devolução</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($borrowings as $book)
                <tr>
                    <td>{{ $book->title }}</td>
                    <td>{{ \Carbon\Carbon::parse($book->pivot->borrowed_at)->format('d/M/Y H:i') }}</td>
                    <td>
                        @if($book->pivot->returned_at)
                            <span class="badge bg-success">Devolvido em {{ \Carbon\Carbon::parse($book->pivot->returned_at)->format('d/M/Y H:i') }}</span>
                        @else
                            <span class="badge bg-warning text-dark">Em Aberto</span>
                        @endif
                    </td>
                    <td>
                        @if(is_null($book->pivot->returned_at))
                            <form action="{{ route('borrowings.return', $book->pivot->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-warning">
                                    <i class="bi bi-arrow-counterclockwise"></i> Devolver
                                </button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Voltar para Usuários
    </a>
</div>
@endsection