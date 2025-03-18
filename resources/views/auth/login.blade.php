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
    <br>
    <button type="submit" class="btn btn-primary">Entrar</button>
</form>
</body>
@endsection