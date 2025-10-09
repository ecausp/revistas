<div class="md-6">

    <div class="mb-1">
        <label for="servico" class="form-label">Serviços</label>

        <select class="form-control select2" id="servico_id" name="servicos[]" multiple="multiple">
            @php
                $currentPedido = $pedido ?? null; 
                $loopServices = $servicosComLimite ?? $servicos;
            @endphp
            
            @foreach ($loopServices as $item)
            @php
                // Acessa o objeto Servico
                $servico = $item->servico ?? $item;

                // Lê a quantidade atual APENAS se o $currentPedido existir.
                $quantidadeAtual = ($currentPedido && $currentPedido->servicos) 
                                   ? ($currentPedido->servicos->find($servico->id)?->pivot->quantidade ?? 0) 
                                   : 0;
                
                // Na Criação, o limite é lido do $item. Na Edição, este campo será vazio.
                $quantidadeDisponivel = $item->quantidade_disponivel ?? ''; 

                // Lógica de Empresa (Consistente)
                $dataEmpresa = $servico->empresas->first()->razao_social ?? "Esse serviço não está associado a nenhuma empresa!";

            @endphp

            <option 
                value="{{ $servico->id }}" 
                data-empresa="{{ $dataEmpresa }}"
                data-descricao="{{ $servico->descricao }}" 
                data-unidade="{{ $servico->unidade_medida }}"
                data-valor="{{ number_format($servico->valor_unitario, 2, ',', '.') }}"
                
                {{-- Data-attribute específico para a tela de Criação para validação de limite --}}
                @if($quantidadeDisponivel) data-quantidade-disponivel="{{ $quantidadeDisponivel }}" @endif
                
                {{-- Data-attribute específico para a tela de Edição para carregar o valor salvo --}}
                @if($quantidadeAtual) data-quantidade="{{ $quantidadeAtual }}" @endif 
                
                {{-- Seleciona na Edição (Verifica se $currentPedido existe antes de usar contains) --}}
                @if ($currentPedido && $currentPedido->servicos->contains($servico->id)) selected @endif>
                
                {{ $servico->descricao }} - {{ $servico->unidade_medida }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- Tabela onde os inputs de quantidade são renderizados --}}
    <div id="tabela-servicos-selecionados" class="mt-3"></div>

    {{-- Aviso de Irregularidade (Mesmo ID em ambas as telas) --}}
    <div id="aviso-irregular" class="alert alert-danger mt-2" style="display:none;">
        Não foi possível cadastrar o pedido. Há um serviço sem empresa associada. Entre em contato com o setor financeiro.
    </div>


<div class="mb-1">
        <label for="observacoes">Observações</label>
        <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $pedido->observacoes ?? '') }}</textarea>
    </div>

    {{-- CAMPOS ESCONDIDOS: Otimizados para pegar os dados do objeto disponível --}}
    @php
        // Define a fonte principal dos dados: Pedido (Edição) ou Solicitante (Criação)
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
