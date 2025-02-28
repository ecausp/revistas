<!-- resources/views/licitacoes/index.blade.php -->
@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h5">
      Licitações
    </div>
    <div class="card-body">
    @can('admin')
    <a href="{{ route('licitacoes.create') }}" class="btn btn-primary mb-3">Criar Nova Licitação</a>
    <table class="table table-striped dataTable no-footer responsive">
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
                        <a href="{{ route('licitacoes.show', $licitacao) }}" class="btn btn-primary"><i class="fas fa-eye"></i> </a>
                        <a href="{{ route('licitacoes.edit', $licitacao) }}" class="btn btn-warning"><i class="fas fa-edit"></i></a>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endcan
    </div>
@endsection