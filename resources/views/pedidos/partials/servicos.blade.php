@if ($pedido->servicos->count() > 0)
<div class="card mt-2 mb-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th colspan="5">Serviços</th>
            </tr>
            <tr>
                <th>Descrição</th>
                <th>Unidade de Medida</th>
                <th>Valor Unitário (R$)</th>
                <th>Quantidade</th>
                <th>Valor Total (R$)</th>
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
            <tr>
                <td>{{ $servico->descricao }}</td>
                <td>{{ $servico->unidade_medida }}</td>
                <td>{{ number_format($servico->valor_unitario, 2, ',', '.') }}</td>
                <td>{{ number_format($servico->pivot->quantidade, 0, ',', '.') }}</td>
                <td>{{ number_format($valorTotalServico, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td class="text-right"><strong>Valor total do pedido: </strong></td>
                <td class="text-right"><strong>{{ number_format($valorTotalPedido, 2, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
@endif