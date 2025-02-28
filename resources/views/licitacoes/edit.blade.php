<!-- resources/views/licitacoes/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Editar Licitação
    </div>
    @can('admin')
    <div class="card-body">
        <form action="{{ route('licitacoes.update', $licitacao) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-1">
                <label for="descricao" class="form-label">Descrição</label>
                <input type="text" class="form-control" name="descricao" id="descricao"
                    value="{{ $licitacao->descricao }}" required></input>
            </div>

            <div class="mb-1">
                <label for="ano" class="form-label">Ano</label>
                <input type="month" class="form-control" name="ano" id="ano" value="{{ $licitacao->ano }}" required>
            </div>

            <div class="mb-1">
                <label for="valor_total" class="form-label">Valor Total</label>
                <input type="number" class="form-control" name="valor_total" id="valor_total"
                    value="{{ $licitacao->valor_total }}" required>
            </div>

            <div class="container mt-5 col">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                    Voltar</a>
            </div>
        </form>
    </div>
    @endcan
    @endsection