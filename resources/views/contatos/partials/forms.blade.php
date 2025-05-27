@php
$licitacao_id = \App\Models\EmpresaLicitacao::all()->where('empresa_id', '=', $empresa->id)->first()->licitacao_id;
$licitacao = \App\Models\Licitacao::find($licitacao_id);
@endphp

<div class="mb-1">
    <label for="name" class="form-label">Nome</label>
    <input type="text" class="typeahead form-control" name="name" id="search" type="text">
</div>

<div id="mensagem-usuario" class="alert alert-danger alert-dismissible fade show mt-3" role="alert"
    style="display: none;">
    <span id="mensagem-texto">Este contato já está cadastrado na empresa <br> {{ $licitacao->descricao }}</span>
    <button type="button" class="btn-close" id="fechar-mensagem" aria-label="Fechar"></button>
    <br>
</div>

<div class="mb-1">
    <label for="email" class="form-label">E-mail</label>
    <input type="email" class="form-control" name="email" id="email" readonly>
</div>

<div class="mb-1">
    <label for="telefone" class="form-label">Telefone</label>
    <input type="text" class="form-control telefone9digitos" name="telefone" id="telefone" placeholder="(99) 99999-9999"
        readonly>
</div>

<div class="mb-1">
    <input type="hidden" class="form-control" name="empresa_id" id="empresa_id" value="{{ $empresa->id }}">
</div>

<div class="container mt-5 ml-0 col">
    <button type="submit" id="cadastrar" class="btn btn-primary"><i class="fas fa-save"></i> Cadastrar</button>
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>