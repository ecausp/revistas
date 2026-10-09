@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Serviços em Execução
    </div>
    <div class="card-body">
        <x-table>
            <thead>
                <tr>
                    <th style="width: 10%;">Nº do Pedido</th>
                    <th style="width: 25%;">Empresa</th>
                    <th style="width: 35%;">Serviço</th>
                    <th style="width: 20%;">Num Empenho</th>
                    <th style="width: 10%;">Prazo (dias)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaUnificada as $item)
                    @php
                        $ps = $item->pedido_servico; // O objeto PedidoServico original
                        $empresa = $item->empresa;   // O objeto com dados da Empresa (ou null)
                    @endphp

                <tr>
                    <td>
                        <a href="{{ route('pedidos.show', $ps->pedido) }}">
                            {{ $ps->pedido->id }}
                        </a>
                    </td>
            
                    {{-- Coluna da Empresa --}}
                    <td>
                        @if($empresa)
                            {{ $empresa->razao_social }}
                            {{-- Você pode até colocar mais dados agora se quiser, ex: --}}
                            {{-- <br><small>{{ $empresa->cnpj }}</small> --}}
                        @else
                            <span class="text-muted">Não identificada</span>
                        @endif
                    </td>

                    <td>{{ $ps->servico->descricao }}</td>
                    <td>{{ $ps->num_empenho ?? '-' }}</td>
                    <td class="text-right">
                        {{ $ps->servico->prazo }} {!! $ps->diasRestantesBadge() !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
    </div>
</div>

@endsection
