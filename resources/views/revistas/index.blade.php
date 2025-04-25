<!-- resources/views/revistas/index.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Revistas
    </div>
    <div class="card-body">
        @can('limites')
        <a href="{{ route('revistas.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Revistas</a>
        <table
            class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Centro de Despesas</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($revistas as $revista)
                <tr>
                    <td>{{ $revista->name }}</td>
                    <td>{{ $revista->centro_despesa }}</td>
                    <td>
                        <a href="{{ route('revistas.show', $revista) }}" class="btn btn-primary"><i
                                class="fas fa-eye"></i> </a>
                        <a href="{{ route('revistas.edit', $revista) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endcan
    </div>
    @endsection