<!-- resources/views/servicos/edit.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.aviso_teste')

<div class="card">
    <div class="card-header h5">
        Editar Serviço
    </div>
    <div class="card-body">

        @can("admin-financeiro")

        <form action="{{ route('servicos.update', $servico) }}" method="POST">
            @csrf
            @method('PUT')
            @include('servicos.partials.forms')
        </form>
    </div>
    @endcan
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