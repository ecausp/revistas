<!-- resources/views/limites/show.blade.php -->
@extends('layouts.app')

@section('content')


<div class="card">
    <div class="card-header h5">
        Limite: {{ $limite->servico->descricao }} - {{ $limite->servico->unidade_medida }}
    </div>
    @can("admin-financeiro")
    <div class="card-body">
        <p><strong>Licitação: </strong>{{ $limite->licitacao->descricao }} - {{ $limite->licitacao->ano }}</p>

        @if($limite->servico->empresas()->first() != NULL)
        <p><strong>Empresa: </strong>{{ $limite->servico->empresas()->first()->razao_social }}</p>
        @else
        <p><strong>Empresa: </strong><span style="color: red;">Esse serviço não está associado a nenhuma empresa</span></p>
        @endif
        
        <p><strong>Revista: </strong>{{ $limite->revista->name }} - {{ $limite->revista->centro_despesa }}</p>
        <p><strong>Prazo: </strong> {{ number_format($limite->servico->prazo, 0, ',', '.') }} dias.</p>
        <p><strong>Valor Unitário :</strong> R$ {{ number_format($limite->valor_unitario, 2, ',', '.') }}</p>
        <p><strong>Quantidade: </strong> {{ number_format($limite->quantidade, 0, ',', '.') }} </p>
        <p><strong>Valor Total de Limite: </strong> R$
            {{ number_format(($limite->quantidade * $limite->valor_unitario), 2, ',', '.') }}
        </p>


        @include('partials.editor')

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