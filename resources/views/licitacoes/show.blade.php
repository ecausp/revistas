<!-- resources/views/licitacoes/show.blade.php -->
@extends('layouts.app')

@section('content')


<div class="card">
    <div class="card-header h5">
        Licitação: {{ $licitacao->descricao }}
    </div>
    <div class="card-body">
        <p><strong>Ano:</strong> {{ $licitacao->ano }}</p>
        <p><strong>Valor Total (R$):</strong> {{ number_format($licitacao->valor_total, 2, ',', '.') }}</p>

        @include('licitacoes.partials.empresas')
        @include('licitacoes.partials.saldos')
        @include('partials.editor')

        <div class="container mt-5 col">
            <a href="{{ route('licitacoes.edit', $licitacao) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                Editar</a>

            @include('licitacoes.partials.delete')

            <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>

        </div>

    </div>
</div>
@endsection

@section('javascripts_bottom')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection