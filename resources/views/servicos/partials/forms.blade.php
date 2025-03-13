<div class="mb-1">
    <label for="descricao" class="form-label">Descrição: </label>
    <input type="text" class="form-control" name="descricao" id="descricao" value="{{ old('descricao', isset($servico) ? $servico->descricao
        : '') }}" placeholder="Serviço..." required></input>
</div>

<div class="mb-1">
    <label for="quantidade" class="form-label">Quantidade: </label>
    <input type="text" class="form-control numero" name="quantidade" id="quantidade" value="{{ old('quantidade', isset($servico) ? $servico->quantidade
        : '') }}" placeholder="000.000.000.000.000" required></input>
</div>

<div class="mb-1">
    <label for="unidade_medida" class="form-label">Unidade de Medida</label>
    <input type="text" class="form-control" name="unidade_medida" id="unidade_medida" value="{{ old('unidade_medida', isset($servico) ? $servico->unidade_medida
        : '') }}" placeholder="laudas, palavras, caracteres, páginas, milheiros, unidades, a0, a1, a2, a3, a4, a5, carta" required></input>
</div>

<div class="mb-1">
    <label for="valor_unitario" class="form-label">Valor Unitário</label>
    <input type="text" class="form-control money" name="valor_unitario" id="valor_unitario" value="{{ old('valor_unitario', isset($servico) ? $servico->valor_unitario
        : '') }}" placeholder="000.000.000.000.000,00" required></input>
</div>

<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
    <a href="{{ route('servicos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>