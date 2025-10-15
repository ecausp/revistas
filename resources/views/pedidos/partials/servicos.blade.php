@if ($pedido->servicos->count() > 0)
<div class="card mt-2 mb-3">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 me-2 mr-5">Serviços</h5>
        </div>
    </div>

    <table class="table table-hover datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive mb-0">
        <thead>
            <tr>
                <th style="width: 15%;">Empresa</th>
                <th style="width: 25%;">Descrição</th>
                <th style="width: 10%;">Prazo (dias)</th>
                <th style="width: 10%;">Nº do Empenho</th>
                <th style="width: 10%;">Unidade de Medida</th>
                <th style="width: 10%;">Valor Unitário (R$)</th>
                <th style="width: 10%;">Quantidade</th>
                <th style="width: 10%;">Valor Total (R$)</th>

            </tr>
        </thead>
        <tbody>
            @php $valorTotalPedido = 0; @endphp
            @foreach ($pedido->servicos as $servico)
                @php
                    $valorTotalServico = $servico->pivot->quantidade * $servico->valor_unitario;
                    $valorTotalPedido += $valorTotalServico;
                @endphp
                <tr id="servico-{{ $servico->id }}">
                    <td>{{ $servico->empresas->first()->razao_social }}</td>
                    <td>{{ $servico->descricao }}</td>
                    <td class="text-right">{{ number_format($servico->prazo, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $servico->pivot->num_empenho}}</td>
                    <td>{{ $servico->unidade_medida }}</td>
                    <td class="text-right">{{ number_format($servico->valor_unitario, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($servico->pivot->quantidade, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($valorTotalServico, 2, ',', '.') }}</td>

                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6"></td>
                <td class="text-right"><strong>Valor total do pedido: </strong></td>
                <td class="text-right"><strong>{{ number_format($valorTotalPedido, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
@endif
