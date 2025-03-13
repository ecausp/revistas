<!-- resources/views/servicos/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Detalhes da servico
    </div>
    @can('admin')
    <div class="card-body">
        <p><strong>Descrição:</strong> {{ $servico->descricao }}</p>
        <p><strong>Quantidade:</strong> {{ $servico->quantidade }}</p>
        <p><strong>Unidade de Medida</strong>{{ $servico->unidade_medida }}</p>
        <p><strong>Valor Unitario</strong>{{ $servico->valor_unitario }}</p>

        <div class="container mt-5 col">
            <a href="{{ route('servicos.edit', $servico) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                Editar</a>

            @include('servicos.partials.delete')

            <a href="{{ route('servicos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>

        </div>
    </div>
    @endcan
</div>
@endsection