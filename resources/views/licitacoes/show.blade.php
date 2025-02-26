<!-- resources/views/licitacoes/show.blade.php -->
@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h4">
      Detalhes da Licitação
    </div>
    @can('admin')
    <p><strong>Descrição:</strong> {{ $licitacao->descricao }}</p>
    <p><strong>Ano:</strong> {{ $licitacao->ano }}</p>
    <p><strong>Valor Total:</strong> {{ number_format($licitacao->valor_total, 2, ',', '.') }}</p>
    <a href="{{ route('licitacoes.index') }}">Voltar</a>
    @endcan
@endsection
