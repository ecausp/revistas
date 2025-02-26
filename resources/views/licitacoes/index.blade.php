<!-- resources/views/licitacoes/index.blade.php -->
@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h4">
      Licitações
    </div>
    @can('admin')
    <a href="{{ route('licitacoes.create') }}">Criar Nova Licitação</a>
    <table class="table table-striped dataTable no-footer">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Ano</th>
                <th>Valor Total</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($licitacoes as $licitacao)
                <tr>
                    <td>{{ $licitacao->descricao }}</td>
                    <td>{{ $licitacao->ano }}</td>
                    <td>{{ number_format($licitacao->valor_total, 2, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('licitacoes.show', $licitacao) }}">Visualizar</a>
                        <a href="{{ route('licitacoes.edit', $licitacao) }}">Editar</a>
                        <form action="{{ route('licitacoes.destroy', $licitacao) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endcan
@endsection