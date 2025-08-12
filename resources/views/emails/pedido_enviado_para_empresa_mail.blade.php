<h1>Pedido #{{ $pedido->id }}</h1>

<p><strong>Revista:</strong> {{ $pedido->revista->nome }}</p>
<p><strong>Status atual:</strong> {{ $pedido->status->label() }}</p>
<p><strong>Observações:</strong> {{ $pedido->observacoes ?? 'Nenhuma observação' }}</p>

<hr>
<p>Enviado automaticamente pelo sistema em {{ now()->format('d/m/Y H:i') }}.</p>
