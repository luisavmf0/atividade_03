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

    <div class="card mb-4">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <strong>Controle e Histórico de Empréstimos</strong>
            
            @if($book->users()->whereNull('returned_at')->exists())
                <span class="badge bg-danger">Indisponível (Emprestado)</span>
            @else
                <span class="badge bg-success">Disponível para Empréstimo</span>
            @endif
        </div>
        
        <div class="card-body">
            <!-- [AJUSTE DE SEGURANÇA]: Apenas admin e bibliotecario podem ver o formulário de empréstimo -->
            @if(auth()->user()->role !== 'cliente')
                @if(!$book->users()->whereNull('returned_at')->exists())
                    <div class="p-3 mb-4 bg-light border rounded">
                        <h5 class="mb-3 text-dark">Realizar Novo Empréstimo</h5>
                        <form action="{{ route('books.borrow', $book) }}" method="POST">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-8 mb-3 mb-md-0">
                                    <label for="user_id" class="form-label font-weight-bold">Selecione o Usuário:</label>
                                    <select class="form-select" id="user_id" name="user_id" required>
                                        <option value="" selected disabled>Escolha um usuário da lista...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-journal-plus"></i> Confirmar Empréstimo
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            @endif

            <h5 class="mb-3">Movimentações Recentes</h5>
            @if($historicoEmprestimos->isEmpty())
                <p class="text-muted mb-0">Nenhum registro de movimentação encontrado para este livro.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Usuário</th>
                                <th>Data de Empréstimo</th>
                                <th>Data de Devolução</th>
                                <th>Ações / Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historicoEmprestimos as $emprestimo)
                                <tr>
                                    <td>
                                        <strong>{{ $emprestimo->user->name }}</strong><br>
                                        <small class="text-muted">{{ $emprestimo->user->email }}</small>
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($emprestimo->borrowed_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        @if($emprestimo->returned_at)
                                            <span class="badge bg-success">
                                                {{ \Carbon\Carbon::parse($emprestimo->returned_at)->format('d/m/Y H:i') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger">Em Aberto</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_null($emprestimo->returned_at))
                                            <!-- [AJUSTE DE SEGURANÇA]: Apenas admin e bibliotecario podem receber a devolução -->
                                            @if(auth()->user()->role !== 'cliente')
                                                <form action="{{ route('borrowings.return', $emprestimo->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-warning btn-sm shadow-sm">
                                                        <i class="bi bi-arrow-counterclockwise"></i> Receber Devolução
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> Com o leitor</span>
                                            @endif
                                        @else
                                            <span class="text-muted text-success"><i class="bi bi-check-circle-fill"></i> Finalizado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <a href="{{ route('books.index') }}" class="btn btn-secondary mb-4">
        <i class="bi bi-arrow-left"></i> Voltar para Lista de Livros
    </a>
</div>
@endsection