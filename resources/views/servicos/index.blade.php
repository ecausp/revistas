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
                    <th>Unidade de Medida</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($servicos as $servico)
                <tr>
                    <td>{{ $servico->descricao }}</td>
                    <td>{{ $servico->unidade_medida }}</td>
                    <td>
                        <a href="{{ route('servicos.show', $servico) }}" class="btn btn-primary"><i
                                class="fas fa-eye"></i> </a>
                                
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('financeiro'))
                        <a href="{{ route('servicos.edit', $servico) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
    </div>
    @endsection