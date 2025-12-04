<div class="card mt-4">
    <div class="card-header h5">
        <div class="d-flex align-items-center" style="cursor: pointer;">
            Histórico
        </div>
    </div>


    <div id="historicoPedidoCard">
        <div class="card-body p-2">
            @if($historico->isEmpty())
                <p class="text-center text-muted mb-0">Nenhum histórico registrado.</p>
            @else
                <div>
                <x-table-simples>
                        <thead>
                            <tr>
                                <th style="width: 20%;">Data</th>
                                <th style="width: 20%;">Pessoa</th>
                                <th style="width: 20%;">Status</th>
                                <th style="width: 40%;">Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historico as $item_historico)
                                <tr style="font-size: .85em">
                                    <td>{{ $item_historico->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $item_historico->pessoa->name }}</td>
                                     <td class="d-flex align-items-center">
                                        <span class="text-secondary" style="font-style: italic;">{{ $item_historico->status_pedido->label() }}</span></td>
                                    <td>{{ $item_historico->observacao ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-table-simples>
                </div>
            @endif
        </div>
    </div>

