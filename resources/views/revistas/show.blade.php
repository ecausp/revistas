<!-- resources/views/revistas/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Revista: {{ $revista->name }}
    </div>
    @can("admin-financeiro")
    <div class="card-body">
        <p><strong>Centro de Despesa: </strong>{{ $revista->centro_despesa }}</p>
        <p><strong>Responsável pelo Centro de Despesa: </strong>{{ $revista->responsavel_centro_despesa }}</p>

        @if(!$revista->solicitantes->isEmpty())
        <h5 class="mt-4">Solicitantes</h5>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Codpes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revista->solicitantes as $solicitante)
                <tr>
                    <td>{{ $solicitante->user->name ?? 'N/A' }}</td>
                    <td>{{ $solicitante->user->email ?? 'N/A' }}</td>
                    <td>{{ $solicitante->user->codpes ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @include('partials.editor')

        <div class="container mt-5 col">
            <a href="{{ route('revistas.edit', $revista) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                Editar</a>

            @include('revistas.partials.delete')

            <a href="{{ route('revistas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>
        </div>
    </div>
    @endcan
</div>
@endsection