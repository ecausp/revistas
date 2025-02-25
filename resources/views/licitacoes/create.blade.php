<!-- resources/views/licitacoes/create.blade.php -->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Licitação</title>
</head>
<body>
    <h1>Criar Licitação</h1>
    <form action="{{ route('licitacoes.store') }}" method="POST">
        @csrf
        <label for="descricao">Descrição:</label>
        <input type="text" name="descricao" id="descricao" required><br>
        <label for="ano">Ano:</label>
        <input type="text" name="ano" id="ano" required><br>
        <label for="valor_total">Valor Total:</label>
        <input type="text" name="valor_total" id="valor_total" required><br>
        <button type="submit">Salvar</button>
    </form>
</body>
</html>
