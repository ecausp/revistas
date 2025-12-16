@php
    $statusAtual = $pedido->status;
    
    // Mapeamento de status e classes de cor (Mantido igual)
    $status = [
        'em_elaboracao' => [
            'label' => 'Revista',
            'icon' => 'fa-plus',
            'sub_label' => 'Em Elaboração',
            'color' => 'warning',
        ],
        'em_analise_empresa' => [
            'label' => 'Empresa',
            'icon' => 'fa-glasses',
            'sub_label' => 'Em Análise',
            'color' => 'success',
        ],
        'em_analise_financeiro' => [
            'label' => 'Financeiro',
            'icon' => 'fa-glasses',
            'sub_label' => 'Em Análise',
            'color' => 'info',
        ],
        'em_execucao' => [
            'label' => 'Empresa',
            'icon' => 'fa-tools',
            'sub_label' => 'Em Execução',
            'color' => 'success',
        ],
        'em_conferencia' => [
            'label' => 'Revista',
            'icon' => 'fa-user-check',
            'sub_label' => 'Em Conferência',
            'color' => 'warning',
        ],
        'em_pagamento' => [
            'label' => 'Financeiro',
            'icon' => 'fa-hand-holding-usd',
            'sub_label' => 'Em Pagamento',
            'color' => 'info',
        ],
        'concluido' => [
            'label' => 'Financeiro',
            'icon' => 'fa-check-circle',
            'sub_label' => 'Concluido',
            'color' => 'primary',
        ],
    ];

    $ordemStatus = array_keys($status);
@endphp

<style>
    /* Comportamento padrão (Desktop) - Mantém o seu espaçamento original */
    .coluna-timeline {
        min-width: auto;
    }
    
    /* Comportamento Mobile - Ativa o modo scroll */
    @media (max-width: 991px) {
        .coluna-timeline {
            min-width: 110px; /* Força o estouro da tela */
        }
        .coluna-seta {
            min-width: 40px;
        }
        /* No celular, a tabela precisa ocupar 100% pra scrollar, ignorando o w-80 */
        .tabela-timeline {
            width: 100% !important; 
        }
    }
</style>

{{-- 
    1. table-responsive: Cria a barra de rolagem se estourar.
    2. pb-3: Um padding bottom para a barra de rolagem não ficar colada nos ícones.
--}}
<div class="table-responsive pb-3">
    
    <table class="w-80 mx-auto tabela-timeline">
        <tr>
            @foreach($status as $nomeStatus => $itemStatus)
                @php
                    $posicaoAtual = array_search($statusAtual, $ordemStatus);
                    $posicaoItem = array_search($nomeStatus, $ordemStatus);
                    $isPassadoOuAtual = $posicaoItem <= $posicaoAtual;

                    $corStatus = $isPassadoOuAtual ? $itemStatus['color'] : 'secondary';
                    $corDescricao = $isPassadoOuAtual ? 'dark' : 'secondary';
                @endphp

                <td class="text-{{ $corStatus }} text-center p-2" style="min-width: 110px;">
                    <span style="font-size: .8em; font-style: italic; white-space: nowrap;">{{ $itemStatus['label'] }}</span><br />
                    <i class="mt-2 text-{{ $corStatus }} fas {{ $itemStatus['icon'] }} border border-{{ $corStatus }} rounded-pill p-2" style="width: 35px; height: 35px; font-size: 1.2em;"></i><br />
                    <span class="text-{{ $corDescricao }}" style="font-size: .8em; font-style: italic; white-space: nowrap;">{{ $itemStatus['sub_label'] }}</span>
                </td>

                {{-- Seta entre os status --}}
                @if (!$loop->last)
                    @php
                        $posicaoProximo = array_search(array_keys($status)[$loop->index + 1], $ordemStatus);
                        $corSeta = $posicaoAtual >= $posicaoProximo ? 'dark' : 'secondary';
                    @endphp

                    {{-- min-width na seta também para ela não sumir --}}
                    <td class="text-center p-2" style="min-width: 40px;">
                        <i class="fas fa-long-arrow-alt-right text-{{ $corSeta }} p-2" style="width: 45px; height: 45px;"></i>
                    </td>
                @endif
            @endforeach
        </tr>
    </table>
</div>