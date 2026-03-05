@extends('layouts.app')

@section('content')


<div class="card">
    <div class="card-header h5">
        Editar Serviço  "{{ $relacao->servico->descricao }}"
    </div>
    <div class="card-body">

        @can("admin-financeiro")

        <p><strong>Licitação: </strong> {{ $relacao->licitacao->descricao }} - {{ $relacao->licitacao->ano }}</p>
        <p><strong>Empresa:</strong> {{ $relacao->empresa->razao_social }}</p>
        <form action="{{ route('servicos-licitacoes.update', $relacao) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-1">
                <label for="valor_unitario" class="form-label">Valor Unitário </label>
                <input type="text" class="form-control money" name="valor_unitario" id="valor_unitario" value="{{ old('valor_unitario', isset($relacao->valor_unitario) ? $relacao->valor_unitario
                    : '') }}" placeholder="Valor Unitário..." required></input>
            </div>
            <div class="mb-1">
                <label for="prazo" class="form-label">Prazo </label>
                <input type="text" class="form-control numero" name="prazo" id="prazo" value="{{ old('prazo', isset($relacao->prazo) ? $relacao->prazo
                    : '') }}" placeholder="Prazo (dias)" required></input>
            </div>


            <div class="container mt-5 col">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                <a href="{{ route('servicos-licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i> Voltar</a>
            </div>

        </form>
    </div>
    @endcan
</div>
@endsection

@section('javascripts_bottom')
@parent
<script>
$(document).ready(function() {
    // Formato número
    $('.numero').mask('000.000.000.000.000', {
        reverse: true
    });
    // Formato moeda
    $('.money').mask('000.000.000.000.000,00', {
        reverse: true
    });
});
</script>
@endsection