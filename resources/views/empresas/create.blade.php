<!-- resources/views/empresas/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Criar Empresa
    </div>
    <div class="card-body">
        @can('admin')
        <form action="{{ route('empresas.store') }}" method="POST">
            @csrf
            @include('empresas.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection