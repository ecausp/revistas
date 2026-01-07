<!-- resources/views/empresas/index.blade.php -->
@extends('layouts.app')

@section('content')


@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Empresas
    </div>
    <div class="card-body">
        <a href="{{ route('empresas.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Empresa</a>
        <x-table>
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
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('financeiro'))
                        <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>
                        <a href="{{ route('empresas.relacionar-licitacoes-servicos', $empresa) }}" title="Associar Serviços" class="btn btn-primary"><i
                                class="fas fa-plus"></i></a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
    </div>
    @endsection