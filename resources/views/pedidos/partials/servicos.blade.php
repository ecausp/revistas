@if ($pedido->servicos->count() > 0)
<div class="card mt-2 mb-3">
    <div class="card-header">
        <div class="d-flex align-items-center">
            <h5 class="mb-0 me-2 mr-5">Serviços</h5>
        </div>
    </div>

    <div class="card-body p-0">
        
        <x-table-simples>
            <thead>
                <tr>
                    <th style="width: 25%;">Empresa</th> {{-- Aumentei um pouco pois nomes de empresa são longos --}}
                    <th style="width: 25%;">Descrição</th>
                    <th style="width: 10%;">Prazo (dias)</th>
                    <th style="width: 10%;">Nº do Empenho</th>
                    <th style="width: 10%;">Unidade de Medida</th>
                    <th style="width: 10%;">Valor Unitário (R$)</th>
                    <th style="width: 5%;">Quantidade</th>
                    <th style="width: 10%;">Valor Total (R$)</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $valorTotalPedido = 0; 
                @endphp

                @foreach ($pedido->servicos as $servico)
                    @php
                        // 1. Busca dados específicos da relação (Empresa e Prazo) no mapa enviado pelo Controller
                        $dadosRelacao = $dados->get($servico->id);
                        $nomeEmpresa = $dadosRelacao->razao_social ?? 'Empresa não identificada';
                        $prazo = $dadosRelacao->prazo ?? $servico->prazo; // Usa o da licitação, ou fallback para o do serviço

                        // 2. Dados do Pivot (O que foi gravado no Pedido)
                        $quantidade = $servico->pivot->quantidade;
                        $valorUnitario = $servico->pivot->valor_unitario; // Agora está salvo corretamente no banco
                        $numEmpenho = $servico->pivot->num_empenho;

                        // 3. Cálculos
                        $valorTotalServico = $quantidade * $valorUnitario;
                        $valorTotalPedido += $valorTotalServico;
                    @endphp

                    <tr id="servico-{{ $servico->id }}">
                        {{-- Empresa Corrigida --}}
                        <td>{{ $nomeEmpresa }}</td>
                        
                        <td>{{ $servico->descricao }}</td>
                        
                        <td class="text-right">
                            {{ number_format($prazo, 0, ',', '.') }} 
                            {!! $servico->pivot->diasRestantesBadge() !!}
                        </td>
                        
                        <td class="text-right">{{ $numEmpenho ?? '-' }}</td>
                        
                        <td>{{ $servico->unidade_medida }}</td>
                        
                        <td class="text-right">{{ number_format($valorUnitario, 2, ',', '.') }}</td>
                        
                        <td class="text-right">{{ number_format($quantidade, 0, ',', '.') }}</td>
                        
                        <td class="text-right">{{ number_format($valorTotalServico, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            
            <tfoot>
                <tr class="d-none d-md-table-row bg-light">
                    <td colspan="6"></td>
                    <td class="text-right"><strong>Valor total do pedido: </strong></td>
                    <td class="text-right"><strong>{{ number_format($valorTotalPedido, 2, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </x-table-simples>

        {{-- 4. BLOCO EXCLUSIVO PARA CELULAR --}}
        <div class="d-block d-md-none p-3 bg-light border-top text-right">
            <span class="font-weight-bold">Valor Total do Pedido:</span><br>
            <span class="text-primary">{{ number_format($valorTotalPedido, 2, ',', '.') }}</span>
        </div>

    </div>
</div>
@endif