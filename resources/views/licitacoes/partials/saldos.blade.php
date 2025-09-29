@if (isset($saldos) && $saldos->count() > 0)
<div class="card mt-2 mb-3">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th colspan="3">Recursos financeiros</th>
            </tr>
            <tr>
                <th>Revista</th>
                <th>Recurso recebido (R$)</th>
                <th>Recurso reservado para estágio (R$)</th>
                <th>Valor contratado (R$)</th>
                <th>Saldo (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($saldos as $saldo)
            <tr>
                <td>{{ $saldo->revista->name}}</td>
                <td class="text-right">{{ number_format($saldo->recurso_recebido, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($saldo->recurso_estagio, 2, ',', '.') }}</td>
                <td class="text-right">
                    {{ number_format($saldo->valor_contratado, 2, ',', '.') }}
                    <button class="btn btn-primary" onclick="window.location='{{ route('saldos.show', $saldo->id) }}'" title="Detalhamento dos serviços contradados">
                        <i class="fas fa-list"></i>
                    </button>
                </td>
                <td class="text-right">{{ number_format(($saldo->recurso_recebido - $saldo->recurso_estagio - $saldo->valor_contratado), 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
