@extends('layouts.app')

@section('content')

@include('partials.messages')

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
                @foreach ($pedidoServicos as $ps)
                <tr>
                    <td>
                        <a href="{{ route('pedidos.show', $ps->pedido) }}">
                            {{ $ps->pedido->id }}
                        </a>
                    </td>
                    
                    <td>
                        @php
                            // Cria a chave para buscar no mapa (se necessário)
                            $chaveMap = $ps->pedido->licitacao_id . '-' . $ps->servico_id;
                        @endphp

                        @if(isset($empresa))
                            {{-- CASO 1: Visão do Contato (Variável global única) --}}
                            {{ $empresa->razao_social }}
                        @elseif(isset($mapaEmpresas) && isset($mapaEmpresas[$chaveMap]))
                            {{-- CASO 2: Visão do Admin (Busca no Mapa enviado pelo controller) --}}
                            {{ $mapaEmpresas[$chaveMap] }}
                        @endif
                    </td>

                    <td>{{ $ps->servico->descricao }}</td>
                    <td>{{ $ps->num_empenho }}</td>
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