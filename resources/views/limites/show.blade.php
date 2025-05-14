<!-- resources/views/limites/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        limite: {{ $limite->servico->descricao }} - {{ $limite->servico->unidade_medida }}
    </div>
    @can('admin-revista')
    <div class="card-body">
        <p><strong>Licitação: </strong>{{ $limite->licitacao->descricao }} - {{ $limite->licitacao->ano }}</p>
        <p><strong>Revista: </strong>{{ $limite->revista->name }} - {{ $limite->revista->centro_despesa }}</p>
        <p><strong>Valor Unitário :</strong> R$ {{ number_format($limite->valor_unitario, 2, ',', '.') }}</p>
        <p><strong>Quantidade: </strong> {{ number_format($limite->quantidade, 0, ',', '.') }} </p>
        <p><strong>Valor Total: </strong> R$
            {{ number_format(($limite->licitacao->valor_total - ($limite->quantidade * $limite->valor_unitario)), 2, ',', '.') }}
        </p>

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