<div class="mb-1">
    <label for="cnpj" class="form-label">CNPJ</label>
    <input type="text" class="form-control" name="cnpj" id="cnpj" value="{{ old('cnpj', isset($empresa) ? $empresa->cnpj
        :"XX.XXX.XXX/0001-XX") }}" required></input>
</div>

<div class="mb-1">
    <label for="razao_social" class="form-label">Razão Social</label>
    <input type="text" class="form-control" name="razao_social" id="razao_social" value="{{ old('razao_social', isset($empresa) ? $empresa->razao_social
        : 'razao_social') }}" required>
</div>


<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary">Atualizar</button>
    <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>