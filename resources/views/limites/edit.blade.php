<!-- resources/views/limites/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Licitação
    </div>
    <div class="card-body">

        @can('revistas.financeiro')
        <form action="{{ route('limites.update', $limite) }}" method="POST">
            @csrf
            @method('PUT')
            @include('limites.partials.forms')
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
@endsection