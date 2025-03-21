@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Conceito
    </div>
    <div class="card-body">
        <div class="card-body" style="font-size:16px">
            <p>
                Sistema para solicitação de serviços de gráfica e editora.
            </p>
            @if (auth()->check() === false)

            <p>
                <a href="{{ route('login') }}" class="btn btn-success"><i class="fas fa-sign-in-alt"
                        aria-hidden="true"></i> Entrar
                    como
                    Empresa</a>
            </p>

            @endif
        </div>
    </div>
</div>
@endsection