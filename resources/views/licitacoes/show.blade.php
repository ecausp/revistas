<!-- resources/views/licitacoes/show.blade.php -->
@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h5">
      Detalhes da Licitação
    </div>
    @can('admin')
<div class ="card-body">
    <p><strong>Descrição:</strong> {{ $licitacao->descricao }}</p>
    <p><strong>Ano:</strong> {{ $licitacao->ano }}</p>
    <p><strong>Valor Total:</strong> {{ number_format($licitacao->valor_total, 2, ',', '.') }}</p>
    <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary">Voltar</a>
    <a href="{{ route('licitacoes.edit', $licitacao) }}" class="btn btn-primary">Editar</a>
    <form action="{{ route('licitacoes.destroy', $licitacao) }}" method="POST" style="display:inline;">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger">Excluir</button>
    </form>
</div>
    @endcan
@endsection
