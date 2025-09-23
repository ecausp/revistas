<!-- resources/views/servicos/create.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.aviso_teste')

<div class="card">
    <div class="card-header h5">
        Criar Serviço
    </div>
    <div class="card-body">
        @can("admin-financeiro")
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
<!-- Select2 principal -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Tradução para português do Brasil -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>


<script>

$(document).ready(function() {
    $('.select2').select2({
        language: "pt-BR"
    });
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