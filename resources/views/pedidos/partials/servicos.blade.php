@if ($pedido->servicos->count() > 0)
<div class="card mt-2 mb-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th colspan="4">Servicos</th>
            </tr>
            <tr>
                <th>Descrição</th>
                <th>Unidade de Medida</th>
                <th>Valor Unitário (R$)</th>
                <th>Quantidade</th>
                <th>Valor Total (R$)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedido->servicos as $servico)
            <tr>
                <td>{{ $servico->descricao }}</td>
                <td>{{ $servico->unidade_medida }}</td>
                <td>{{ number_format($servico->valor_unitario, 2, ',', '.') }}</td>
                <td>{{ number_format($servico->pivot->quantidade, 0, ',', '.') }}</td>
                <td>{{ number_format($servico->pivot->quantidade * $servico->valor_unitario, 2, ',', '.') }}</td>
                <td>
                    <a href="{{ route('servicos.show', $servico) }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif