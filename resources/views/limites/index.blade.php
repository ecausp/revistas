<!-- resources/views/limites/index.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Limites
    </div>
    <div class="card-body">
        @can("admin-financeiro")
        <a href="{{ route('limites.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Limite</a>
        <x-table>
            <thead>
                <tr>
                    <th>Licitação</th>
                    <th>Revista</th>
                    <th>Empresa</th>
                    <th>Serviço</th>
                    <th>Prazo (dias)</th>
                    <th>Quantidade</th>
                    <th>Valor Unitário (R$)</th>
                    <th>Valor Total de Limite (R$)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($limites as $limite)
                <tr>
                    <td>{{ $limite->licitacao->descricao }}</td>
                    <td>{{ $limite->revista->name }}</td>

                    @if($limite->servico->empresas()->first() != NULL)
                    <td>{{ $limite->servico->empresas()->first()->razao_social }}</td>
                    @else
                    <td style="color: red;">Esse serviço não está associado a nenhuma empresa</td>
                    @endif

                    <td>{{ $limite->servico->descricao }}</td>
                    <td class="text-right">{{ number_format($limite->servico->prazo, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($limite->quantidade, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($limite->valor_unitario, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format(($limite->quantidade * $limite->valor_unitario), 2, ',', '.') }} </td>

                    <td>
                        <a href="{{ route('limites.show', $limite) }}" class="btn btn-primary"><i
                                class="fas fa-eye"></i> </a>
                        <a href="{{ route('limites.edit', $limite) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
        @endcan
    </div>
    @endsection