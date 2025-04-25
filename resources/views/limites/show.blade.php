<!-- resources/views/limites/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        limite: {{ $limite->id }}
    </div>
    @can('limites')
    <div class="card-body">
        <p><strong>Licitação: </strong>{{ $limite->licitacao->descricao }} - {{ $limite->licitacao->ano }}</p>
        <p><strong>Revista: </strong>{{ $limite->revista->name }} - {{ $limite->revista->centro_despesa }}</p>
        <p><strong>Serviço: </strong>{{ $limite->servico->descricao }} - {{ $limite->servico->unidade_medida }}</p>


        <div class="container mt-5 col">
            <a href="{{ route('limites.edit', $limite) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                Editar</a>

            @include('limites.partials.delete')

            <a href="{{ route('limites.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>

        </div>
    </div>
    @endcan
</div>
@endsection