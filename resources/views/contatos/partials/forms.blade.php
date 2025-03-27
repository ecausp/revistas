<div class="mb-1">
    <label for="name" class="form-label">Nome</label>
    <input type="text" class="typeahead form-control" name="name" id="search" type="text">
</div>

<div class="mb-1">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" name="email" id="email" placeholder="email@exemplo.com" readonly>
</div>

<div class="mb-1">
    <label for="telefone" class="form-label">Telefone</label>
    <input type="text" class="form-control telefone9digitos" name="telefone" id="telefone" placeholder="(99) 99999-9999"
        readonly>
</div>

<div class="mb-1">
    <input type="hidden" class="form-control" name="empresa_id" id="empresa_id"
        placeholder="{{ $empresa->razao_social }}" value="{{ $empresa->id }}">
</div>

<span id="mensagem-usuario" style="color: red; display: none; font-size: 14px;">Usuário já cadastrado no banco de
    dados</span>

<div class="container mt-5 ml-0 col">
    <button type="submit" id="cadastrar" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar</button>
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>