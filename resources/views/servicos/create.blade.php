<!-- resources/views/servicos/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Criar Serviço
    </div>
    <div class="card-body">
        @can('admin-revista')
        <form action="{{ route('servicos.store') }}" method="POST">
            @csrf
            @include('servicos.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection

@section('javascripts_bottom')
@parent
<script>
$(document).ready(function() {
    // Formato número
    $('.numero').mask('000.000.000.000.000', {
        reverse: true
    });
    // Formato moeda
    $('.money').mask('000.000.000.000.000,00', {
        reverse: true
    });
});
</script>
@endsection