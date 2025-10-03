<!-- resources/views/revistas/show.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Revista: {{ $revista->name }}
    </div>
    <div class="card-body">
        <p><strong>Centro de Despesa: </strong>{{ $revista->centro_despesa }}</p>
        <p><strong>Editor(es): </strong>
            @foreach (explode(',', $revista->responsavel_centro_despesa) as $editorRevista)
                {{ \Uspdev\Replicado\Pessoa::dump($editorRevista)['nompesttd'] }} @if (!$loop->last) , @endif
            @endforeach
        </p>
        <p><strong>E-mail: </strong>{{ $revista->email_revista }}</p>

        @include('revistas.partials.solicitantes')
        @include('revistas.partials.saldos')
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