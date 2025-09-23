<!-- resources/views/revistas/show.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Revista: {{ $revista->name }}
    </div>
    <div class="card-body">
        <p><strong>Centro de Despesa: </strong>{{ $revista->centro_despesa }}</p>
        <p><strong>Responsável pelo Centro de Despesa: </strong>{{ $revista->responsavel_centro_despesa }}</p>

        @include('revistas.partials.solicitantes')

        @include('partials.editor')

        <div class="container mt-5 col">
                @can('admin-financeiro')
            <a href="{{ route('revistas.edit', $revista) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                Editar</a>

            @include('revistas.partials.delete')

    @endcan
            <a href="{{ route('revistas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>
        </div>
    </div>
</div>
@endsection