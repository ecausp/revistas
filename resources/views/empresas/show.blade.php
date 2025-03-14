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

        <p><strong>Contatos:</strong></p>
        <table
            class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contatos as $contato)
                <tr>
                    <td>{{ $contato->name }}</td>
                    <td>{{ $contato->email }}</td>
                    <td>{{ $contato->telefone }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>


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