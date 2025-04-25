<!-- resources/views/licitacoes/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Licitação
    </div>
    <div class="card-body">

        @can('licitacoes')
        <form action="{{ route('licitacoes.update', $licitacao) }}" method="POST">
            @csrf
            @method('PUT')
            @include('licitacoes.partials.forms')
        </form>
    </div>
    @endcan
</div>
@endsection

{{-- 
Não encontrei o viewMode years no JQuery Datepicker do Laravel USP Theme.
Precisei utilizar o Bootstrap Datepicker.
TODO: Verificar se o Bootstrap Datepicker funciona com o Laravel USP Theme.  
--}}

@section('styles')
@parent
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('javascripts_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@parent
<script>
$(document).ready(function() {
    $('#ano').datepicker({
        format: "yyyy", // Exibir apenas o ano
        viewMode: "years", // Iniciar na visualização de anos
        minViewMode: "years", // Impedir mudança para meses/dias
        autoclose: true // Fecha automaticamente ao selecionar o ano
    });

    // Formato moeda
    $('.money').mask('000.000.000.000.000,00', {
        reverse: true
    });
});
</script>
@endsection