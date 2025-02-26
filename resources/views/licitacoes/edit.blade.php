<!-- resources/views/licitacoes/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <h1>Editar Licitação</h1>
    @can('admin')
    <form action="{{ route('licitacoes.update', $licitacao) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="descricao">Descrição:</label>
        <input type="text" name="descricao" id="descricao" value="{{ $licitacao->descricao }}" required><br>
        <label for="ano">Ano:</label>
        <input type="text" name="ano" id="ano" value="{{ $licitacao->ano }}" required><br>
        <label for="valor_total">Valor Total:</label>
        <input type="text" name="valor_total" id="valor_total" value="{{ $licitacao->valor_total }}" required><br>
        <button type="submit">Atualizar</button>
    </form>
    @endcan
@endsection
