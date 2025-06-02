<!-- resources/views/pedidos/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Pedido da Revista {{ $pedido->revista->name }} para a Licitação {{ $pedido->licitacao->descricao }}
    </div>
    @can("revista")
    <div class="card-body">
        @include('pedidos.partials.cabecalho')

        <p><strong>Observações:</strong> {{ $pedido->observacoes }}</p>

        @include('pedidos.partials.servicos')
    </div>

</div>
@endcan
</div>
@endsection

@section('javascripts_bottom')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection