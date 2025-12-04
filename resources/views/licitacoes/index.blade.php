<!-- resources/views/licitacoes/index.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Licitações
    </div>
    <div class="card-body">
        @if(auth()->user()->hasRole('financeiro'))
            <a href="{{ route('licitacoes.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i>
            Licitação</a>
        @endif

        <x-table>
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
                        @if(auth()->user()->hasRole('financeiro'))
                        <a href="{{ route('licitacoes.edit', $licitacao) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>
                        @endif        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
    </div>
    @endsection