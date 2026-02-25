@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Autenticação da Empresa
    </div>

    <div class="card-body">
        <form action="{{ route('login_empresa') }}" method="POST">
            @csrf
            <div class="mb-1">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control email" name="email" id="email" placeholder="email@exemplo.com"
                    required></input>
            </div>
            <div class="mb-1">
                <label for="password" class="form-label">Senha</label>
                <input type="password" class="form-control password" name="password" id="password"
                    placeholder="senha..." required></input>
            </div>
            <br>


            <div class="container mt-5 col">

                <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                    Entrar</button>
                <a href="{{ route('password.request') }}" class="btn btn-secondary"><i class="fas fa-question"
                        aria-hidden="true"></i>

                    Esqueci a Senha</a>
            </div>
        </form>
    </div>
    @endsection


    @section('javascripts_bottom')
    @parent
    <script>
    $(document).ready(function() {
        // Formato número
        $('.numero').mask('000.000.000.000.000', {
            reverse: true
        });
        // Formato moeda
        $('.money').mask('000.000.000.000.000,00', {
            reverse: true
        });
    });
    </script>
    @endsection