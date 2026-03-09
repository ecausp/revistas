<!-- resources/views/revistas/index.blade.php -->
@extends('layouts.app')

@section('content')


@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Revistas
    </div>
    <div class="card-body">
        <a href="{{ route('revistas.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Revistas</a>
        <x-table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Centro de Despesas</th>
                    <th>Editor(es)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($revistas as $revista)
                <tr>
                    <td>{{ $revista->name }}</td>
                    <td>{{ $revista->centro_despesa }}</td>
                    <td>
                        @foreach (explode(',', $revista->responsavel_centro_despesa) as $editorRevista)
                            {{ \Uspdev\Replicado\Pessoa::dump($editorRevista)['nompesttd'] ?? $editorRevista }} @if (!$loop->last) , @endif
                        @endforeach
                    </td>
                    <td>
                        <a href="{{ route('revistas.show', $revista) }}" class="btn btn-primary" title="Visualizar Revista"><i
                                class="fas fa-eye"></i> </a>
                        <a href="{{ route('revistas.edit', $revista) }}" class="btn btn-primary" title="Editar Revista"><i
                                class="fas fa-edit"></i></a>
                        <a href="{{ route('solicitantes.create',  $revista)}}" class="btn btn-primary" title="Cadastrar Solicitante"><i 
                                class="fas fa-user"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
    </div>
    @endsection