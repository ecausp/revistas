@extends('layouts.app')

@section('content')

<h2>Redefinir Senha</h2>

@if(session('error'))
<p style="color: red;">{{ session('error') }}</p>
@endif

<form action="{{ route('password.update') }}" method="POST">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-1">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control email" name="email" id="email" placeholder="email@exemplo.com"
            required></input>
    </div>

    <div class="mb-1">
        <label for="senha" class="form-label">Nova Senha</label>
        <input type="password" class="form-control" name="password" id="password" required></input>
    </div>

    <div class="mb-1">
        <label for="senha" class="form-label">Nova Senha</label>
        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
            required></input>
    </div>

    <button type="submit" class="btn btn-primary">Alterar Senha</button>
</form>
</body>
@endsection