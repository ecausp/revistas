<div class="mb-1">
    <label for="descricao" class="form-label">Descrição: </label>
    <input type="text" class="form-control descricao" name="descricao" id="descricao" value="{{ old('descricao', isset($servico) ? $servico->descricao
        : '') }}" placeholder="Serviço..." required></input>
</div>

<div class="mb-1">
    <label for="quantidade" class="form-label">Quantidade: </label>
    <input type="text" class="form-control quantidade" name="quantidade" id="quantidade" value="{{ old('quantidade', isset($servico) ? $servico->quantidade
        : '') }}" placeholder="00" required></input>
</div>

<div class="mb-1">
    <label for="unidade_medida" class="form-label">Unidade de Medida</label>
    <input type="text" class="form-control unidade_medida" name="unidade_medida" id="unidade_medida" value="{{ old('unidade_medida', isset($servico) ? $servico->unidade_medida
        : '') }}" placeholder=" " required></input>
</div>

<div class="mb-1">
    <label for="valor_unitario" class="form-label">Valor Unitário</label>
    <input type="text" class="form-control valor_unitario" name="valor_unitario" id="valor_unitario" value="{{ old('valor_unitario', isset($servico) ? $servico->valor_unitario
        : '') }}" placeholder="R$ 00,00" required></input>
</div>


<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary">{{ isset($servico) ? "Atualizar" : "Salvar"}}</button>
    <a href="{{ route('servicos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>