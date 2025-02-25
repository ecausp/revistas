<!-- resources/views/licitacoes/edit.blade.php -->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Licitação</title>
</head>
<body>
    <h1>Editar Licitação</h1>
    <form action="{{ route('licitacoes.update', $licitacao) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="descricao">Descrição:</label>
        <input type="text" name="descricao" id="descricao" value="{{ $licitacao->descricao }}" required><br>
        <label for="ano">Ano:</label>
        <input type="text" name="ano" id="ano" value="{{ $licitacao->ano }}" required><br>
        <label for="valor_total">Valor Total:</label>
        <input type="text" name="valor_total" id="valor_total" value="{{ $licitacao->valor_total }}" required><br>
        <button type="submit">Atualizar</button>
    </form>
</body>
</html>
