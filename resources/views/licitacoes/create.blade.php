
<!-- resources/views/licitacoes/create.blade.php -->
@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header h5">
      Criar Licitação
    </div>
    <div class="card-body">
    @can('admin')
      <form action="{{ route('licitacoes.store') }}" method="POST">
        @csrf
        <label for="descricao">Descrição:</label>
        <input type="text" name="descricao" id="descricao" required><br>
        <label for="ano">Ano:</label>
        <input type="text" name="ano" id="ano" required><br>
        <label for="valor_total">Valor Total:</label>
        <input type="text" name="valor_total" id="valor_total" required><br>
        <button type="submit">Salvar</button>
      </form>
    @endcan
    </div>
@endsection
