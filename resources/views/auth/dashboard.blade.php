@extends('layouts.app')

@section('content')

<body>
    <h2>Bem-vindo!</h2>
    <p><strong>Nome:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger">Logout</button>

    </form>
</body>

@endsection