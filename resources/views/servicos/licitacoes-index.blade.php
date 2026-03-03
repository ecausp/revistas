<!-- resources/views/servicos/index.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Serviços
    </div>
    <div class="card-body">
        <a href="{{ route('servicos.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Serviço</a>
        <x-table>
            <thead>
                <tr>
                    <th>Serviço</th>
                    <th>Empresa</th>
                    <th>Licitação</th>
                    <th>Unidade de Medida</th>
                    <th>Valor Unitário</th>
                    <th>Prazo (dias)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servicos as $servico)
                <tr>
                    <td>{{ $servico->servico->descricao }}</td>
                    <td>{{ $servico->empresa->razao_social }}</td>
                    <td>{{ $servico->licitacao->descricao }} - {{ $servico->licitacao->ano }}</td>
                    <td>{{ $servico->servico->unidade_medida }}</td>
                    <td style="text-align: right;">{{ $servico->valor_unitario }}</td>
                    <td style="text-align: right;">{{ $servico->prazo }}</td>
                    <td>
                        <a href="{{ route('servicos.show', $servico->servico) }}" class="btn btn-primary"><i
                                class="fas fa-eye"></i> </a>
                                
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('financeiro'))
                        @if(!$servico->servico->limites()->exists())
                        
                        <a href="{{ route('servicos-licitacoes.edit', $servico->servico) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>
                        @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
    </div>
    @endsection