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
    <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i></a>
    <a href="{{ route('licitacoes.edit', $licitacao) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
    <form action="{{ route('licitacoes.destroy', $licitacao) }}" method="POST" style="display:inline;">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i></button>
    </form>
</div>
    @endcan
@endsection
