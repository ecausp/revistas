<div class="mb-1">
    <label for="descricao" class="form-label">Descrição </label>
    <input type="text" class="form-control" name="descricao" id="descricao" value="{{ old('descricao', isset($servico) ? $servico->descricao
        : '') }}" placeholder="Serviço..." required></input>
</div>

<div class="mb-1">
    <label for="valor_unitario" class="form-label">Valor Unitário</label>
    <input type="text" class="form-control money" name="valor_unitario" id="valor_unitario" value="{{ old('valor_unitario', isset($servico) ? $servico->valor_unitario
        : '') }}" placeholder="0,00" required></input>
</div>

<div class="mb-1">
    <label for="unidade_medida" class="form-label">Unidade de Medida</label>

    @if (isset($servico))
    <select class="form-control select2" name="unidade_medida" id="unidade_medida">
        <option value="{{ $servico->unidade_medida }}" selected>{{ $servico->unidade_medida }} </option>
        @else
        <select class="form-control select2" name="unidade_medida" id="unidade_medida">
            <option value="" selected>Unidade de Medida...</option>
            @endif
            @foreach ($unidades_medida as $unidade_medida)
            <option value="{{ $unidade_medida }}">{{ $unidade_medida }}</option>
            @endforeach
        </select>
</div>


<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
    <a href="{{ route('servicos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>