<div class="mb-1">
    <label for="name" class="form-label">Nome</label>
    <input type="text" class="typeahead form-control" name="name" id="search" type="text">
</div>

<div id="mensagem-usuario" class="alert alert-danger alert-dismissible fade show mt-3" role="alert"
    style="display: none;">
    <span id="mensagem-texto">Este solicitante já está cadastrado na revista <br> {{ $revista->name }}</span>
    <button type="button" class="btn-close" id="fechar-mensagem" aria-label="Fechar"></button>
    <br>
</div>


<div class="container mt-5 ml-0 col">
    <button type="submit" id="cadastrar" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar</button>
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>