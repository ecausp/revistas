@extends('layouts.app')

@section('content')


@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Ajuda
    </div>
    <div class="card-body">
        <a href="{{ route('ajuda.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i> Ajuda</a>
        <table
            class="table table-hover datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive tabela">
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Categoria</th>
                    <th>URL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ajudas as $ajuda)
                <tr>
                    <td>{{ $ajuda->descricao }}</td>
                    <td>{{ $ajuda->categoria }}</td>
                    <td>
                        <a href="https://youtube.com/watch?v={{ $ajuda->url }}" class="btn btn-primary"><i
                                class="fas fa-eye"></i> Assistir </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endsection