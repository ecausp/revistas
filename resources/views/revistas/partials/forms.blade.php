<div class="mb-1">
    <label for="name" class="form-label">Nome: </label>
    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', isset($revista) ? $revista->name
        : '') }}" placeholder="Revista..." required></input>
</div>

<div class="mb-1">
    <label for="centro_despesa" class="form-label">Centro de Despesa: </label>
    <input type="text" class="form-control" name="centro_despesa" id="centro_despesa" value="{{ old('centro_despesa', isset($revista) ? $revista->centro_despesa
        : '') }}" placeholder="Centro de Despesa..." required></input>
</div>

<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
    <a href="{{ route('revistas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>