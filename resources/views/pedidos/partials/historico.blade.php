{{-- Card Histórico do Pedido (Colapsável) --}}
<div class="card mt-4">
        <div class="card-header h5">
        <div class="d-flex align-items-center"data-bs-toggle="collapse" data-bs-target="#historicoPedidoCard" style="cursor: pointer;">
            Histórico do Pedido {{ $pedido->id }} <i class="fas fa-chevron-down ml-3" id="historicoIcon"></i>
           
        </div>
    </div>


    <div class="collapse" id="historicoPedidoCard">
        <div class="card-body p-2">
            @if($historico->isEmpty())
                <p class="text-center text-muted mb-0">Nenhum histórico registrado.</p>
            @else
                <div class="table-responsive">
                <table class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 20%;">Data de Alteração</th>
                                <th style="width: 20%;">Pessoa</th>
                                <th style="width: 20%;">Status</th>
                                <th style="width: 40%;">Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historico as $item_historico)
                                <tr>
                                    <td>{{ $item_historico->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $item_historico->pessoa->name }}</td>
                                     <td class="text-{{ $item_historico->status_pedido->color() }}">{{ $item_historico->status_pedido->label() }} <i class="{{ $item_historico->status_pedido->icon() }}" style="{{ $item_historico->status_pedido->icon_style() }}"></i></td>
                                    <td>{{ $item_historico->observacao ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
