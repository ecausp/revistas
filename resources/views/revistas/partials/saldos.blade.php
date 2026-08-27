@if (isset($saldos) && $saldos->count() > 0)
<div class="card mt-2 mb-3">
    <x-table-simples>
        <thead>
            <tr>
                <th colspan="3">Recursos Financeiros</th>
            </tr>
            <tr>
                <th class="text-center">Licitação</th>
                <th class="text-center">Recurso Recebido (R$)</th>
                <th class="text-center">Recurso Reservado para Estágio (R$)</th>
                <th class="text-center">Valor Contratado (R$)</th>
                <th class="text-center">Saldo (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($saldos as $saldo)
            <tr>
                <td>{{ $saldo->licitacao->descricao }}</td>
                <td class="text-right">
                    @if (filled($saldo->observacao))
                        <button type="button" class="btn btn-link btn-sm p-0 mr-1"
                            data-toggle="modal" data-target="#observacaoSaldoModal{{ $saldo->id }}"
                            title="Ver observação" aria-label="Ver observação">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    @endif
                    {{ number_format($saldo->recurso_recebido, 2, ',', '.') }}
                </td>
                <td class="text-right">{{ number_format($saldo->recurso_estagio, 2, ',', '.') }}</td>
                <td class="text-right">
                    {{ number_format($saldo->valor_contratado, 2, ',', '.') }}
                    @if ($saldo->valor_contratado > 0)
                        <button class="btn btn-primary" onclick="window.location='{{ route('saldos.show', $saldo->id) }}'" title="Detalhamento dos serviços contradados">
                            <i class="fas fa-list"></i>
                        </button>
                    @endif
                </td>
                <td class="text-right">{{ number_format(($saldo->recurso_recebido - $saldo->recurso_estagio - $saldo->valor_contratado), 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-table-simples>
    @foreach ($saldos as $saldo)
        @if (filled($saldo->observacao))
            @include('saldos.partials.modal-observacao', ['saldo' => $saldo])
        @endif
    @endforeach
</div>
@endif
