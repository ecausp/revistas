<!-- resources/views/empresas/index.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Pedidos
    </div>
    <div class="card-body">
        @can("acesso-revista")
        <a href="{{ route('pedidos.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Pedido</a>
        @endcan
        <table
            class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive">
            <thead>
                <tr>
                    <th>Revista</th>
                    <th>Licitação</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pedidos as $pedido)
                <tr>
                    <td>{{ $pedido->revista->name }}</td>
                    <td>{{ $pedido->licitacao->descricao }} - {{ $pedido->licitacao->ano }}</td>
    	            <td>
                        <div class="d-flex align-items-center">
                            <i class="{{$pedido->status->icon()}} mr-2" style="{{ $pedido->status->icon_style() }}"></i>
                            <span class="text-secondary" style="font-size: .9rem; font-style: italic;">{{ $pedido->status->label() }}</span>
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-primary"><i
                                class="fas fa-eye"></i> </a>
                        <a href="{{ route('pedidos.edit', $pedido ) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endsection
