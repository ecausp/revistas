@extends('layouts.app')

@section('content')

<h2>Login</h2>
@if(session('error'))
<p style="color: red;">{{ session('error') }}</p>
@endif
<form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="mb-1">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control email" name="email" id="email" placeholder="email@exemplo.com"
            required></input>
    </div>
    <div class="mb-1">
        <label for="password" class="form-label">Senha</label>
        <input type="password" class="form-control password" name="password" id="password" placeholder="senha..."
            required></input>
    </div>
    <br>

    @if (Auth::check())
    <p>usuario logado</p>
    @endif

    <div class="container mt-5 col">

        <button type="submit" class="btn btn-primary">Entrar</button>
        <a href="{{ route('password.request') }}" class="btn btn-secondary">
            Esqueci a Senha</a>
    </div>
</form>
</body>
@endsection