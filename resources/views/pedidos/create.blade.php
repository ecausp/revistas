<!-- resources/views/licitacoes/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Criar Pedido para {{ $solicitante }}
    </div>
    <div class="card-body">
        @can("admin-financeiro")
        @include('pedidos.partials.cabecalho')
        @endcan
    </div>

</div>
@endsection