@extends('laravel-usp-theme::master')

{{-- Blocos do laravel-usp-theme --}}
{{-- Ative ou desative cada bloco --}}

{{-- Target:card-header; class:card-header-sticky --}}
@include('laravel-usp-theme::blocos.sticky')

{{-- Target: button, a; class: btn-spinner, spinner --}}
@include('laravel-usp-theme::blocos.spinner')

{{-- Target: table; class: datatable-simples --}}
@include('laravel-usp-theme::blocos.datatable-simples')

{{-- Fim de blocos do laravel-usp-theme --}}

@section('title')
@parent
@endsection

@section('styles')
@parent
<style>
    /* Estilo para a linha da tabela ao passar o mouse */
    .table tbody tr:hover {
        cursor: pointer; /* Muda o cursor para indicar que é interativo */
        transform: scale(1.015); /* Pequeno zoom para dar um efeito sutil */
        box-shadow: 0 3px 7px rgba(0, 0, 0, 0.5); /* Uma sombra leve */
        transition: all 0.2s ease-in-out; /* Transição suave para o efeito */
    }

    /* Se sua tabela tem bordas e você quer que elas também reajam ao hover */
    .table tbody tr:hover td {
        border-color: #d1d1d1; /* Exemplo: uma borda um pouco mais escura no hover */
    }

    /* Adicione outros estilos CSS que você quiser aqui */
</style>
@endsection

@section('javascripts_bottom')
@parent
<script>
// Seu código .js
</script>
@endsection