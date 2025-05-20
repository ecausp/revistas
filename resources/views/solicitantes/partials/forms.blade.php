<div class="mb-1">
    <label for="name" class="form-label">Nome</label>
    <input type="text" class="form-control" name="name" id="search" type="text">
</div>

<div id="mensagem-usuario" class="alert alert-danger alert-dismissible fade show mt-3" role="alert"
    style="display: none;">
    <span id="mensagem-texto">Este solicitante já está cadastrado na revista <br> {{ $revista->name }}</span>
    <button type="button" class="btn-close" id="fechar-mensagem" aria-label="Fechar"></button>
    <br>
</div>


<div id="info-usuario" class="mt-2 text-sm text-gray-700">
    <!-- Aqui vai aparecer o e-mail e codpes -->
</div>

<div class="mb-1">
    <input type="hidden" class="form-control" name="revista_id" id="revista_id" placeholder="{{ $revista->name }}"
        value="{{ $revista->id }}">
</div>

<input type="hidden" name="user_id" id="user_id" value="">


<div class="container mt-5 ml-0 col">
    <button type="submit" id="cadastrar" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar</button>
    <a href="{{ route('revistas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>