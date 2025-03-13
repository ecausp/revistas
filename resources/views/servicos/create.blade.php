<!-- resources/views/servicos/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Criar Servico
    </div>
    <div class="card-body">
        @can('admin')
        <form action="{{ route('servicos.store') }}" method="POST">
            @csrf
            @include('servicos.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection