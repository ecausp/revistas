<!-- resources/views/empresas/index.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Empresas
    </div>
    <div class="card-body">
        @can('admin')
        <a href="{{ route('empresas.create') }}" class="btn btn-primary mb-3">Registrar Nova Empresa</a>
        <table
            class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive">
            <thead>
                <tr>
                    <th>CNPJ</th>
                    <th>Razão Social</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($empresas as $empresa)
                <tr>
                    <td>{{ $empresa->cnpj }}</td>
                    <td>{{ $empresa->razao_social }}</td>
                    <td>
                        <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-primary"><i
                                class="fas fa-eye"></i> </a>
                        <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-warning"><i
                                class="fas fa-edit"></i></a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endcan
    </div>
    @endsection