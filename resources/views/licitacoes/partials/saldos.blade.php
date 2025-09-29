@if (isset($saldos) && $saldos->count() > 0)
<div class="card mt-2 mb-3">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th colspan="3">Saldos</th>
            </tr>
            <tr>
                <th>Revista</th>
                <th>Recurso Recebido (R$)</th>
                <th>Recurso em Estágio (R$)</th>
                <th>Valor Contratado (R$)</th>
                <th>Saldo (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($saldos as $saldo)
            <tr>
                <td>{{ $saldo->revista->name}}</td>
                <td>{{ number_format($saldo->recurso_recebido, 2, ',', '.') }}</td>
                <td>{{ number_format($saldo->recurso_estagio, 2, ',', '.') }}</td>
                <td>{{ number_format($saldo->valor_contratado, 2, ',', '.') }}</td>
                <td>{{ number_format(($saldo->recurso_recebido - $saldo->recurso_estagio - $saldo->valor_contratado), 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
