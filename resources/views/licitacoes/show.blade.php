<!-- resources/views/licitacoes/show.blade.php -->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes da Licitação</title>
</head>
<body>
    <h1>Detalhes da Licitação</h1>
    <p><strong>Descrição:</strong> {{ $licitacao->descricao }}</p>
    <p><strong>Ano:</strong> {{ $licitacao->ano }}</p>
    <p><strong>Valor Total:</strong> {{ number_format($licitacao->valor_total, 2, ',', '.') }}</p>
    <a href="{{ route('licitacoes.index') }}">Voltar</a>
</body>
</html>
