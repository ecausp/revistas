<div class="mb-1">
    <label for="descricao" class="form-label">Descrição</label>
    <input type="text" class="form-control" name="descricao" id="descricao" value="{{ old('descricao', isset($licitacao) ? $licitacao->descricao
        :"Licitação...") }}" required></input>
</div>

<div class="mb-1">
    <label for="ano" class="form-label">Ano</label>
    <input type="month" class="form-control" name="ano" id="ano" value="{{ old('ano', isset($licitacao) ? $licitacao->ano
        : date('Y')) }}" required>
</div>

<div class="mb-1">
    <label for="valor_total" class="form-label">Valor Total</label>
    <input type="text" class="form-control money" name="valor_total" id="valor_total"
        value="{{ old('valor_total', "ARRUMAR") }}" placeholder={{ isset($licitacao) ? $licitacao->valor_total
        : "000.000.000.000" }} required>
</div>

<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary">Atualizar</button>
    <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>