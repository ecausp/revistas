@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header h5">
            Ajuda
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>URL</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ajudas as $ajuda)
                    <tr>
                        <td>{{ $ajuda->descricao }}</td>
                        <td>{{ $ajuda->categoria }}</td>
                        <td>{{ $ajuda->url }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection