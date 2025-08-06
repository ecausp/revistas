<div class="md-6">

    <div class="mb-1">
        <label for="licitacao_id" class="form-label">Licitação</label>
        <select class="form-control select2" name="licitacao_id" id="licitacao_id">
            @foreach ($licitacoes as $licitacao)
            <option value="{{ $licitacao->id }}">{{ $licitacao->descricao }} - {{ $licitacao->ano }}</option>
            @endforeach
        </select>
    </div>

    @if(isset($limite))
        <div class="mb-1">
        <label for="revista_id" class="form-label">Revista</label>
        <select class="form-control select2" name="revista_id[]" id="revista_id" disabled>
            <option value="{{ $limite->revista->id }}">{{ $limite->revista->name }} - {{ $limite->revista->centro_despesa }}</option>
             <input type="hidden" name="revista_id[]" value="{{ $limite->revista->id }}">
        </select>
    </div>
    
    @else
    <div class="mb-1">
        <label for="revista_id" class="form-label">Revista</label>
        <select class="form-control select2" name="revista_id[]" id="revista_id" multiple="multiple">
            @foreach ($revistas as $revista)
            <option value="{{ $revista->id }}">{{ $revista->name }} - {{ $revista->centro_despesa }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="mb-1">
        <label for="servico_id" class="form-label">Serviço</label>
        <select class="form-control select2" name="servico_id" id="servico_id">
            @foreach ($servicos as $servico)
            <option value="{{ $servico->id }}">{{ $servico->descricao }} - {{ $servico->unidade_medida }}</option>
            @endforeach
        </select>
    </div>


    <div class="mb-1">
        <label for="valor_unitario" class="form-label">Valor Unitário</label>
        <input type="text" class="form-control money" name="valor_unitario" id="valor_unitario"
            value="{{ old('valor_unitario', isset($limite) ? $limite->valor_unitario : '') }}" placeholder="0,00"
            readonly>


    </div>

    <div class="mb-1">
        <label for="quantidade" class="form-label">Quantidade</label>
        <input type="text" class="form-control quantidade" name="quantidade" id="quantidade"
            value="{{ old('quantidade', isset($limite) ? $limite->quantidade : '') }}" placeholder="Quantidade licitada"
            required>
    </div>


    <div class="mt-4">
        <label for="quantidade" class="form-label">Valor Disponível</label>

        <div class="border-start border-3 border-primary ps-3">
            <!-- Linha 1: Total da Licitação -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Valor Total da Licitação:</span>
                <span class="fw-bold">
                    <span id="valor_total_licitacao">0,00</span>
                </span>
            </div>

            <!-- Linha 2: Total do Pedido (com sinal negativo) -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Valor Total de Limite:</span>
                <span class="fw-bold text-danger">
                    - <span id="valor_total_pedido">0,00</span>
                </span>
            </div>

            <!-- Linha divisória -->
            <hr class="my-2 border-primary opacity-25">

            <!-- Linha 3: Diferença (resultado) -->
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">Valor Total Disponível:</span>
                <span class="fs-5 fw-bold text-success">
                    <span id="diferenca_valores">0,00</span>
                </span>
            </div>
        </div>
    </div>



    <div class="container mt-5 col">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>

        <a href="{{ route('limites.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
            Voltar</a>

    </div>
</div>