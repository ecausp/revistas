<!-- resources/views/revistas/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Criar Revista
    </div>
    <div class="card-body">
        @can('revistas')
        <form action="{{ route('revistas.store') }}" method="POST">
            @csrf
            @include('revistas.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection