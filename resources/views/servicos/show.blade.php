<!-- resources/views/servicos/show.blade.php -->
@extends('layouts.app')

@section('content')


<div class="card">
    <div class="card-header h5">
        Serviço: {{ $servico->descricao }}
    </div>
    <div class="card-body">
        <p><strong>Unidade de medida: </strong>{{ $servico->unidade_medida }}</p>
        @include('partials.editor')

        <div class="container mt-5 col">

            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('financeiro'))
            <a href="{{ route('servicos.edit', $servico) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                Editar</a>

            @include('servicos.partials.delete')
            @endif
            <a href="{{ route('servicos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>

        </div>
    </div>
</div>
@endsection