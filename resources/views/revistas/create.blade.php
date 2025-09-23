<!-- resources/views/revistas/create.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.aviso_teste')

<div class="card">
    <div class="card-header h5">
        Criar Revista
    </div>
    <div class="card-body">
        @can("admin-financeiro")

        <form action="{{ route('revistas.store') }}" method="POST">
            @csrf
            @include('revistas.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection