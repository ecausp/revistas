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
<style>
.select2-container {
    width: 100% !important;
}
</style>
@endsection

@section('javascripts_bottom')
@parent
<script>
$(document).ready(function() {
    // Formato CNPJ
    $('.cnpj').mask('CC.CCC.CCC/CCCC-00', {
        translation: { 'C': { pattern: /[A-Za-z0-9]/ } }
    }).on('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endsection
