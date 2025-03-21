@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
      Sobre
    </div>
    <div class="card-body">
      <div class="card-body" style="font-size:16px">
        <p>
          Controle orçamentário das revistas da unidade para pedidos de serviços de editora e gráfica.    
        </p>
        
        @can('user')
          <p>
            <a href="{{ route('login') }}" class="btn btn-success"><i class="fas fa-sign-in-alt" aria-hidden="true"></i> Entrar como Empresa</a>
          </p>
        @endcan
      
      </div>
    </div>
</div>
@endsection