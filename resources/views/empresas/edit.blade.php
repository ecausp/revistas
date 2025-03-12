<!-- resources/views/empresas/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Empresa
    </div>
    <div class="card-body">

        @can('admin')
        <form action="{{ route('empresas.update', $empresa) }}" method="POST">
            @csrf
            @method('PUT')
            @include('empresas.partials.forms')
        </form>
    </div>
    @endcan
</div>
@endsection