@extends('layouts.app')

@section('content')


<div class="card">
    <div class="card-header h5">
        Serviços contratados
    </div>

    <div class="card-body">

    <span><strong>Licitação: </strong></span><span>{{ $saldo->licitacao->descricao }} / {{ $saldo->licitacao->ano }}</span><br />  
    <span><strong>Revista: </strong></span><span>{{ $saldo->revista->name }}</span>

    <table class="table table-hover datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive">
        <thead>
            <tr>
                <th>Nº do Pedido</th>
                <th>Descrição do Serviço</th>
                <th>Prazo (dias)</th>
                <th>Quantidade</th>
                <th>Valor Unitário (R$)</th>
                <th>Total (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servicos as $servico)
                <tr>
                    <td>{{ $servico->pedido_id }}</td>
                    <td>{{ $servico->descricao }}</td>
                    <td style="text-align: right;">{{ number_format($servico->prazo, 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($servico->pivot->quantidade, 0, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($servico->valor_unitario, 2, ',', '.') }}</td>
                    <td style="text-align: right;">{{ number_format($servico->pivot->quantidade * $servico->valor_unitario, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">Total Contratado</th>
                <th style="text-align: right;">{{ number_format($saldo->valor_contratado, 2, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>
    
   <div class="container mt-3 col">
        <a href="{{ route('licitacoes.show', $saldo->licitacao_id) }}" class="btn btn-secondary">
            <i class="fas fa-chevron-left"></i> Voltar
        </a>
    </div>
</div>
@endsection
