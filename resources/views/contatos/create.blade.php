<!-- resources/views/contatos/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Cadastrar Contato para a empresa {{ $empresa->razao_social }}
    </div>
    <div class="card-body">
        @can('admin')
        <form action="{{ route('contatos.store') }}" method="POST">
            @csrf
            @include('contatos.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection

@section('javascripts_bottom')
<script>
$(document).ready(function() {
    // Formato cnpj
    $('.telefone9digitos').mask("(00) 90000-0000");
});
</script>
@endsection