<div class="mb-1">
    <label for="descricao" class="form-label">Descrição</label>
    <input type="text" class="form-control" name="descricao" id="descricao" value="{{ old('descricao', isset($licitacao) ? $licitacao->descricao : '') }}" placeholder="Licitação..." required></input>
</div>

<div class="mb-1">
    <label for="ano" class="form-label">Ano</label>
    <input type="month" class="form-control" name="ano" id="ano" value="{{ old('ano', isset($licitacao) ? $licitacao->ano : date('Y')) }}" placeholder="{{ date('Y') }}" required>
</div>

<div class="mb-1">
    <label for="valor_total" class="form-label">Valor Total</label>
    <input type="text" class="form-control money" name="valor_total" id="valor_total"
        value="{{ old('valor_total', isset($licitacao) ? $licitacao->valor_total : '') }}" placeholder="0.000.000.000.000,00" required>
</div>

<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary">
        @if (isset($licitacao))
            <i class="fas fa-save"></i> Atualizar
        @else
            <i class="fas fa-save"></i> Salvar
        @endif
    </button>

    @if (isset($licitacao))
    <a href="{{ route('empresas.create',  $licitacao )}}" class="btn btn-success"><i class="fas fa-building"></i>
        Empresa(s)</a>
    @endif

    <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>