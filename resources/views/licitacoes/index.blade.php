<!-- resources/views/licitacoes/index.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Licitações
    </div>
    <div class="card-body">
        @can("admin-financeiro")
        <a href="{{ route('licitacoes.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i>
            Licitação</a>
        <table
            class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Ano</th>
                    <th>Valor Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($licitacoes as $licitacao)
                <tr>
                    <td>{{ $licitacao->descricao }}</td>
                    <td>{{ $licitacao->ano }}</td>
                    <td class="text-right">{{ number_format($licitacao->valor_total, 2, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('licitacoes.show', $licitacao) }}" class="btn btn-primary"><i
                                class="fas fa-eye"></i> </a>
                        <a href="{{ route('licitacoes.edit', $licitacao) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endcan
    </div>
    @endsection