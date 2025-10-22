<!-- resources/views/pedido-servicos/index.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Serviços em execução
    </div>
    <div class="card-body">

        <table class="table table-hover datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive">
            <thead>
                <tr>
                    <th style="width: 10%;">Nº do Pedido</th>
                    <th style="width: 20%;">Empresa</th>
                    <th style="width: 40%;">Serviço</th>
                    <th style="width: 20%;">Num Empenho</th>
                    <th style="width: 10%;">Prazo (dias)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedidoServicos as $ps)
                <tr>
                    <td>
                        <a href="{{ route('pedidos.show', $ps->pedido) }}">
                            {{ $ps->pedido->id }}
                        </a>
                    </td>
                    <td>{{ $ps->servico->empresas->first()->razao_social}}</td>
                    <td>{{ $ps->servico->descricao }}</td>
                    <td>{{ $ps->num_empenho }}</td>
                    <td class="text-right">
                        {{ $ps->servico->prazo }}{!! $ps->diasRestantesBadge()  !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
