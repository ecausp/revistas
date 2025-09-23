@extends('layouts.app')
<style>
    .nav {
        margin-bottom: 0px !important;
    }

</style>
@section('content')


@include('partials.messages')
<div class="card">
    <div class="card-header h5">
        Sobre
    </div>
    <div class="card-body">
        <div class="card-body" style="font-size:16px">
            <p>
                Controle orçamentário das revistas da unidade para pedidos de serviços de editora e gráfica.
            </p>

            @if (!Auth::check())
            <p>
                <a href="{{ route('login') }}" class="btn btn-success"><i class="fas fa-sign-in-alt"
                        aria-hidden="true"></i> Empresa</a>
            </p>
            @endif

        </div>
    </div>
</div>
@endsection