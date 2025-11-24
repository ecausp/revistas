<div class="mb-3">
    <label class="form-label">Categoria</label>
    <select name="categoria" class="form-control">
        <option value="">Selecione...</option>
        @foreach(\App\Enums\CategoriaAjuda::cases() as $categoria)
            <option value="{{ $categoria->value }}" 
                {{ old('categoria', $ajuda->categoria->value ?? '') == $categoria->value ? 'selected' : '' }}>
                {{ $categoria->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Descrição</label>
    <input type="text" name="descricao" class="form-control" 
           placeholder="Ex: Como emitir boleto"
           value="{{ old('descricao', $ajuda->descricao ?? '') }}">
</div>

<div class="mb-3">
    <label class="form-label">URL do Vídeo</label>
    <input type="text" name="url" class="form-control" 
           placeholder="Cole o link do YouTube aqui"
           value="{{ old('url', $ajuda->url ?? '') }}">
</div>

<button type="submit" class="btn btn-primary">Salvar</button>