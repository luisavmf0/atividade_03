@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Lista de Usuários</h1>

    {{-- Alertas de Sucesso ou Erro --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Débito Pendente</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    
                    <td>
                        @if($user->debit > 0)
                            <span class="text-danger fw-bold">
                                R$ {{ number_format($user->debit, 2, ',', '.') }}
                            </span>
                        @else
                            <span class="text-success">Nenhum débito</span>
                        @endif
                    </td>

                    <td>
                        <div class="d-flex gap-2">
                            <!-- Qualquer usuário autenticado pode visualizar o perfil de um usuário -->
                            <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm text-white">
                                <i class="bi bi-eye"></i> Visualizar
                            </a>

                            <!-- Botão de receber pagamento caso o usuário tenha débito e quem esteja acessando seja admin/bibliotecário -->
                            @if($user->debit > 0 && auth()->user()?->role !== 'cliente')
                                <form action="{{ route('users.clearDebit', $user->id) }}" method="POST" onsubmit="return confirm('Confirmar pagamento de R$ {{ number_format($user->debit, 2, ',', '.') }} e zerar débito de {{ $user->name }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm text-white">
                                        <i class="bi bi-cash-coin"></i> Receber Pagamento
                                    </button>
                                </form>
                            @endif

                            <!-- Apenas administradores poderão ver o botão de Editar para alterar os papéis -->
                            @can('update', $user)
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $users->links() }}
    </div>
</div>
@endsection