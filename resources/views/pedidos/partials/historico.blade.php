<div class="card mt-4">
    <div class="card-header h5">
            Histórico
    </div>

    <div class="card-body">
        @if($historico->isEmpty())
            <p class="text-center text-muted">Nenhum histórico registrado</p>
        @else
            <x-table-simples>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Pessoa</th>
                            <th>Status</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historico as $item_historico)
                            <tr style="font-size: .85em">
                                <td>{{ $item_historico->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $item_historico->pessoa->name }}</td>
                                <td>
                                    <span class="text-secondary" style="font-style: italic;">{{ $item_historico->status_pedido->label() }}</span>
                                </td>
                                <td>{{ $item_historico->observacao ?? '-' }}</td>

                            </tr>
                        @endforeach
                    </tbody>
            </x-table-simples>
        @endif
    </div>
</div>
