<!-- resources/views/licitacoes/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Detalhes da Licitação
    </div>
    @can('admin')
    <div class="card-body">
        <p><strong>Descrição:</strong> {{ $licitacao->descricao }}</p>
        <p><strong>Ano:</strong> {{ $licitacao->ano }}</p>
        <p><strong>Valor Total:</strong> {{ number_format($licitacao->valor_total, 2, ',', '.') }}</p>


        <div class="container mt-3 mb-3 text-right">
            <small class=" text-muted">editado em: {{  $licitacao->updated_at }}
                <br>
                por: {{ $licitacao->editor }}
            </small>
        </div>


        <div class="container mt-5 col">
            <a href="{{ route('licitacoes.edit', $licitacao) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                Editar</a>

            <form action="{{ route('licitacoes.destroy', $licitacao) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Apagar</button>
            </form>

            <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>
        </div>
    </div>
    @endcan
    @endsection