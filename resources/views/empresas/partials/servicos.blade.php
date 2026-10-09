@if ($empresa->licitacoes->count() > 0)
<div class="card mt-2 mb-3">
    <div class="card-header font-weight-bold">Serviços</div>
    <div class="card-body p-0">
        <x-table-simples>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Valor Unitário (R$)</th>
                    <th>Unidade de Medida</th>
                    <th>Prazo (dias)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($empresa->servicos as $els)
            <tr>
                <td>{{ $els->servico->descricao }}</td>
                <td class="text-right">{{ number_format($els->valor_unitario, 2, ',', '.') }}</td>
                <td>{{ $els->servico->unidade_medida }}</td>
                <td class="text-right">{{ number_format($els->prazo, 0, ',', '.') }}</td>
                <td>
                    <a href="{{ route('servicos.show', $els->servico) }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach

            </tbody>
        </x-table-simples>
    </div>
</div>
@endif
