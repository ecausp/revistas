

<div class="mb-1">
    <label for="cnpj" class="form-label">CNPJ</label>
    <input type="text" class="form-control cnpj" name="cnpj" id="cnpj"
        value="{{ old('cnpj', isset($empresa) ? $empresa->cnpj : '') }}" placeholder="00.000.000/0000-00"
        required>
</div>

<div class="mb-1">
    <label for="razao_social" class="form-label">Razão Social</label>
    <input type="text" class="form-control" name="razao_social" id="razao_social"
        value="{{ old('razao_social', isset($empresa) ? $empresa->razao_social : '') }}" required>
</div>


<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>

    <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i> Voltar</a>
</div>
