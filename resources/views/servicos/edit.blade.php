<!-- resources/views/servicos/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar servico
    </div>
    <div class="card-body">

        @can('admin')
        <form action="{{ route('servicos.update', $servico) }}" method="POST">
            @csrf
            @method('PUT')
            @include('servicos.partials.forms')
        </form>
    </div>
    @endcan
</div>
@endsection