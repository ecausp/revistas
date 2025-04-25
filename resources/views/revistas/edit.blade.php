<!-- resources/views/revistas/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Revista
    </div>
    <div class="card-body">

        @can('revistas')
        <form action="{{ route('revistas.update', $revista) }}" method="POST">
            @csrf
            @method('PUT')
            @include('revistas.partials.forms')
        </form>
    </div>
    @endcan
</div>
@endsection