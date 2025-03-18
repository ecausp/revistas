<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esqueci a Senha</title>
</head>

<body>
    <h2>Recuperar Senha</h2>

    @if(session('status'))
    <p style="color: green;">{{ session('status') }}</p>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <button type="submit">Enviar Link de Recuperação</button>
    </form>
</body>

</html>