<!-- resources/views/servicos/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        {{ $servico->descricao }}
    </div>
    @can('admin')
    <div class="card-body">
        <p><strong>Quantidade: </strong>{{ number_format($servico->quantidade, 0, ',', '.') }}</p>
        <p><strong>Unidade de medida: </strong>{{ $servico->unidade_medida }}</p>
        <p><strong>Valor Unitário: </strong>{{ number_format($servico->valor_unitario, 2, ',', '.') }}</p>

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