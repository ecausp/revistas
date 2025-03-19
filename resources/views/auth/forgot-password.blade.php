@extends('layouts.app')

@section('content')

<h2>Recuperar Senha</h2>

@if(session('status'))
<p style="color: green;">{{ session('status') }}</p>
@endif

<form action="{{ route('password.email') }}" method="POST">
    @csrf
    <div class="mb-1">
        <label for="email" class="form-label">Email</label>
        <input type="text" class="form-control email" name="email" id="email" placeholder="email@exemplo.com"
            required></input>
    </div>
    <br>
    <button type="submit" class="btn btn-primary">Enviar Link</button>
</form>
@endsection