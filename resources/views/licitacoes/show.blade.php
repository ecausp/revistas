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

        <small class="text-muted">Atualizado em {{ date_format($licitacao->updated_at, 'd/m/Y H:i') }}
            <br />por {{ $licitacao->editor }}</small>

        <div class="container mt-5 col">
            <a href="{{ route('licitacoes.edit', $licitacao) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                Editar</a>

            @include('licitacoes.partials.delete')

            <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>

        </div>
    </div>
    @endcan
</div>
@endsection