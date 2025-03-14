<!-- resources/views/empresas/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Empresa: {{ $empresa->razao_social }}
    </div>
    @can('admin')
    <div class="card-body">
        <p><strong>CNPJ:</strong> {{ $empresa->cnpj }}</p>

        @include('empresas.partials.contatos')
        @include('empresas.partials.licitacoes')

        <div class="container mt-5 col">
            <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                Editar</a>

            @include('empresas.partials.delete')

            <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>

        </div>
    </div>
    @endcan
</div>
@endsection