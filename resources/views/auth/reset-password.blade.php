<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
</head>

<body>
    <h2>Redefinir Senha</h2>

    @if(session('error'))
    <p style="color: red;">{{ session('error') }}</p>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <label>Nova Senha:</label>
        <input type="password" name="password" required>
        <br>
        <label>Confirmar Nova Senha:</label>
        <input type="password" name="password_confirmation" required>
        <br>
        <button type="submit">Alterar Senha</button>
    </form>
</body>

</html>