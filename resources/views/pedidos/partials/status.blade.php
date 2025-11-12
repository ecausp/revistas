@php
    $statusAtual = $pedido->status;
    
    // Mapeamento de status e classes de cor
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

<table class="w-80 mx-auto">
    <tr>
        @foreach($status as $nomeStatus => $itemStatus)
            @php
                $posicaoAtual = array_search($statusAtual, $ordemStatus);
                $posicaoItem = array_search($nomeStatus, $ordemStatus);
                $isPassadoOuAtual = $posicaoItem <= $posicaoAtual;

                $corStatus = $isPassadoOuAtual ? $itemStatus['color'] : 'secondary';
                $corDescricao = $isPassadoOuAtual ? 'dark' : 'secondary';
            @endphp

            {{-- Célula do status --}}
            <td class="text-{{ $corStatus }} text-center p-2">
                <span style="font-size: .8em; font-style: italic;">{{ $itemStatus['label'] }}</span><br />
                <i class="mt-2 text-{{ $corStatus }} fas {{ $itemStatus['icon'] }} border border-{{ $corStatus }} rounded-pill p-2" style="width: 35px; height: 35px; font-size: 1.2em;"></i><br />
                <span class="text-{{ $corDescricao }}" style="font-size: .8em; font-style: italic;">{{ $itemStatus['sub_label'] }}</span>
            </td>

            {{-- Adiciona a seta entre os status, exceto no último --}}
            @if (!$loop->last)
                @php
                    // Posição do próximo status
                    $posicaoProximo = array_search(array_keys($status)[$loop->index + 1], $ordemStatus);

                    // Se o próximo status já passou, seta 'dark', senão 'secondary'
                    $corSeta = $posicaoAtual >= $posicaoProximo ? 'dark' : 'secondary';
                @endphp
                <td class="text-center p-2">
                    <i class="fas fa-long-arrow-alt-right text-{{ $corSeta }} p-2" style="width: 45px; height: 45px;"></i>
                </td>
            @endif
        @endforeach
    </tr>
</table>
