@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="my-4">Lista de Autores</h1>

    {{-- Apenas admin e bibliotecario podem ver o botão de adicionar autor --}}
    @can('create', App\Models\Author::class)
        <a href="{{ route('authors.create') }}" class="btn btn-success mb-3">
            <i class="bi bi-plus"></i> Adicionar Autor
        </a>
    @endcan

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>E-mail</th> 
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($authors as $author)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $author->name }}</td>
                    <td>{{ $author->email }}</td>
                    <td>
                        <!-- Botão de Visualizar - Disponível para todos -->
                        <a href="{{ route('authors.show', $author) }}" class="btn btn-info btn-sm">
                            <i class="bi bi-eye"></i> Visualizar
                        </a>

                        <!-- Botão de Editar - Apenas admin e bibliotecario -->
                        @can('update', $author)
                            <a href="{{ route('authors.edit', $author) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        @endcan

                        <!-- Botão de Excluir - Apenas admin -->
                        @can('delete', $author)
                            <form action="{{ route('authors.destroy', $author) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" onclick="return confirm('Deseja excluir este autor?')">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhum autor encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection