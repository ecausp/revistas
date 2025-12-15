@if (isset($saldos) && $saldos->count() > 0)
    <div class="card mt-2 mb-3">
        <div class="card-header font-weight-bold">
            Recursos Financeiros
        </div>
        <div class="card-body p-0">
            <x-table-simples>
                <thead>
                    <tr>
                        <th>Revista</th>
                        <th class="text-center">Recurso Recebido (R$)</th>
                        <th class="text-center">Recurso Reservado para Estágio (R$)</th>
                        <th class="text-center">Valor Contratado (R$)</th>
                        <th class="text-center">Saldo (R$)</th>
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
        </div>
    </div>
@endif