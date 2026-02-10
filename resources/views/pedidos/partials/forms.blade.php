<div class="md-6">

    <div class="mb-1">
        <label for="servico" class="form-label">Serviços</label>

        <select class="form-control select2" id="servico_id" name="servicos[]" multiple="multiple">
            @php
                $currentPedido = $pedido ?? null; 
                $loopServices = $servicosComLimite ?? $servicos;
                
                // 1. Descobre qual é a Licitação atual
                $idLicitacaoAtual = $currentPedido->licitacao_id ?? ($licitacao->id ?? null);

                // 2. BUSCA DADOS COMPLETOS DA RELAÇÃO (Empresa + Valor + Prazo)
                $mapaDados = collect([]); 
                
                if ($idLicitacaoAtual) {
                    $mapaDados = \DB::table('empresa_licitacao_servico')
                        ->join('empresas', 'empresa_licitacao_servico.empresa_id', '=', 'empresas.id')
                        ->where('licitacao_id', $idLicitacaoAtual)
                        ->select(
                            'empresa_licitacao_servico.servico_id',
                            'empresa_licitacao_servico.valor_unitario', 
                            'empresa_licitacao_servico.prazo',
                            'empresas.razao_social'
                        )
                        ->get()
                        ->keyBy('servico_id');
                }
            @endphp
            
            @foreach ($loopServices as $item)
            @php
                // Normaliza o objeto
                $servico = $item->servico ?? $item;

                // Tenta pegar os dados da relação (Tabela Tripla)
                $dadosRelacao = $mapaDados->get($servico->id);

                // 1. Empresa
                $nomeEmpresa = $dadosRelacao->razao_social ?? "Esse serviço não está associado a nenhuma empresa nesta licitação!";
                
                // 2. Valor Unitário
                $valorUnitarioRaw = $dadosRelacao->valor_unitario ?? ($servico->valor_unitario ?? 0);
                
                // 3. Prazo
                $prazo = $dadosRelacao->prazo ?? ($servico->prazo ?? '');

                // Quantidade Atual (Edição)
                $quantidadeAtual = 0;
                if ($currentPedido && $currentPedido->servicos) {
                    $itemNoPedido = $currentPedido->servicos->firstWhere('id', $servico->id);
                    if ($itemNoPedido && $itemNoPedido->pivot) {
                        $quantidadeAtual = $itemNoPedido->pivot->quantidade;
                    }
                }
                
                // Quantidade Disponível (Criação)
                $quantidadeDisponivel = $item->quantidade_disponivel ?? ''; 

                // Selected
                $isSelected = ($currentPedido && $currentPedido->servicos->contains('id', $servico->id));
            @endphp

            <option 
                value="{{ $servico->id }}" 
                
                {{-- Dados vindos da tabela tripla --}}
                data-empresa="{{ $nomeEmpresa }}"
                data-valor="{{ number_format($valorUnitarioRaw, 2, ',', '.') }}"
                data-prazo="{{ $prazo }}"

                data-descricao="{{ $servico->descricao }}" 
                data-unidade="{{ $servico->unidade_medida }}"
                
                {{-- Data-attributes condicionais --}}
                @if($quantidadeDisponivel) data-quantidade-disponivel="{{ $quantidadeDisponivel }}" @endif
                @if($quantidadeAtual) data-quantidade="{{ $quantidadeAtual }}" @endif 
                
                @if ($isSelected) selected @endif
            >
                {{ $servico->descricao }} - {{ $servico->unidade_medida }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Tabela onde os inputs de quantidade são renderizados --}}
    <div id="tabela-servicos-selecionados" class="mt-3"></div>

    {{-- Aviso de Irregularidade --}}
    <div id="aviso-irregular" class="alert alert-danger mt-2" style="display:none;">
        Não foi possível cadastrar o pedido. Há um serviço sem empresa associada. Entre em contato com o setor financeiro.
    </div>

    <div class="mb-1">
        <label for="observacoes">Observações</label>
        <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $pedido->observacoes ?? '') }}</textarea>
    </div>

    @php
        $source = $pedido ?? $solicitante; 
    @endphp

    <div class="mb-1">
        <input type="hidden" class="form-control" name="revista_id" id="revista_id"
            value="{{ $source->revista_id ?? $source->revista->id }}">
    </div>

    <div class="mb-1">
        <input type="hidden" class="form-control" name="solicitante_id" id="solicitante_id"
            value="{{ $source->solicitante_id ?? $source->id }}">
    </div>

    <div class="mb-1">
        <input type="hidden" class="form-control" name="licitacao_id" id="licitacao_id"
            value="{{ $pedido->licitacao_id ?? $licitacao->id }}">
    </div>

</div>