<!-- resources/views/empresas/create.blade.php -->
@extends('layouts.app')

@section('content')


<div class="card">
    <div class="card-header h5">
        Criar Empresa
    </div>
    <div class="card-body">
        @can("admin-financeiro")
        <form action="{{ route('empresas.store') }}" method="POST">
            @csrf
            @include('empresas.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection


@section('styles')
@parent
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Tradução para português do Brasil -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>

<style>
.select2-container {
    width: 100% !important;
}
</style>
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('javascripts_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@parent
<script>
$(document).ready(function() {
    // Formato cnpj
    $('.cnpj').mask('CC.CCC.CCC/CCCC-00', {
        translation: {
            'C': { pattern: /[A-Za-z0-9]/ },
        }
    })
    .on('input', function() {
        this.value = this.value.toUpperCase();
    });


    // Inicializa o Select2 de forma responsiva para o campo SERVIÇO
    $('#servico_id').select2({
        width: '100%',
        language: "pt-BR"
    });
});
</script>
@endsection