<!-- resources/views/servicos/index.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Serviços
    </div>
    <div class="card-body">
        @can('revistas.financeiro')
        <a href="{{ route('servicos.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Serviço</a>
        <table
            class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Unidade de Medida</th>
                    <th>Valor Unitário</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servicos as $servico)
                <tr>
                    <td>{{ $servico->descricao }}</td>
                    <td>{{ $servico->unidade_medida }}</td>
                    <td class="text-right">{{ number_format($servico->valor_unitario, 2, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('servicos.show', $servico) }}" class="btn btn-primary"><i
                                class="fas fa-eye"></i> </a>
                        <a href="{{ route('servicos.edit', $servico) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endcan
    </div>
    @endsection