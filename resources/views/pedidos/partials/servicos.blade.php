@if ($pedido->servicos->count() > 0)
<div class="card mt-2 mb-3">
    <div class="card-header">
    <div class="d-flex align-items-center">
        <h5 class="mb-0 me-2 mr-5">Serviços</h5>
    </div>
</div>
    <table class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive mb-0">
        <thead>
            <tr>
                <th>Empresa</th>
                <th>Descrição</th>
                <th>Unidade de Medida</th>
                <th>Valor Unitário (R$)</th>
                <th>Quantidade</th>
                <th>Valor Total (R$)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @php
            $valorTotalPedido = 0;
            @endphp
            @foreach ($pedido->servicos as $servico)
            @php
            $valorTotalServico = $servico->pivot->quantidade * $servico->valor_unitario;
            $valorTotalPedido += $valorTotalServico;
            @endphp
            <tr id="servico-{{ $servico->id }}">
                <td>{{ $servico->empresas->first()->razao_social }}</td>
                <td>{{ $servico->descricao }}</td>
                <td>{{ $servico->unidade_medida }}</td>
                <td class="text-right">{{ number_format($servico->valor_unitario, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($servico->pivot->quantidade, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($valorTotalServico, 2, ',', '.') }}</td>
                <td>
                    <button class="btn btn-danger d-flex justify-content-center remover-servico"  data-pedido-id="{{ $pedido->id }}" data-servico-id="{{ $servico->id }}">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"></td>
                <td class="text-right"><strong>Valor total do pedido: </strong></td>
                <td class="text-right"><strong>{{ number_format($valorTotalPedido, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
@endif