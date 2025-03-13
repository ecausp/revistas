<div class="mb-1">
    <label for="name" class="form-label">Nome</label>
    <input type="text" class="form-control" name="name" id="name" placeholder="Nome" required></input>
</div>

<div class="mb-1">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" name="email" id="email" placeholder="exemplo@email.com" required>
</div>

<div class="mb-1">
    <label for="telefone" class="form-label">Telefone</label>
    <input type="text" class="form-control telefone9digitos" name="telefone" id="telefone" placeholder="(xx) xxxx-xxxx"
        required>
</div>


</div>


<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary">Cadastrar</button>
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>
</div>