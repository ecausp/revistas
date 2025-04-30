<div class="md-6">

    <div class="mb-1">
        <label for="licitacao_id" class="form-label">Licitação</label>
        <select class="form-control select2" name="licitacao_id" id="licitacao_id">
            @foreach ($licitacoes as $licitacao)
            <option value="{{ $licitacao->id }}">{{ $licitacao->descricao }} - {{ $licitacao->ano }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-1">
        <label for="revista_id" class="form-label">Revista</label>
        <select class="form-control select2" name="revista_id" id="revista_id">
            @foreach ($revistas as $revista)
            <option value="{{ $revista->id }}">{{ $revista->name }} - {{ $revista->centro_despesa }}</option>
            @endforeach
        </select>
    </div>

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
            value="{{ old('quantidade', isset($limite) ? $limite->quantidade : '') }}" placeholder="quantidade licitada"
            required>
    </div>


    <div class="mb-1">
        <label for="valor_total" class="form-label">Valor Total</label>
        <input type="text" class="form-control money" name="valor_total" id="valor_total" placeholder="0,00" readonly>
    </div>



    <div class="container mt-5 col">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>

        @if (isset($limite))
        <a href="{{ route('contatos.create',  $limite)}}" class="btn btn-success"><i class="fas fa-user"></i>
            Contato(s)</a>
        @endif

        <a href="{{ route('limites.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
            Voltar</a>

    </div>
</div>