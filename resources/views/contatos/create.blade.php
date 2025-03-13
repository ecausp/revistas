<!-- resources/views/contatos/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Cadastrar Contato
    </div>
    <div class="card-body">
        @can('admin')
        <form action="{{ route('contatos.store') }}" method="POST">
            @csrf

            <label for="name">Nome:</label><br>
            <input type="text" id="name" name="name" placeholder="Nome"><br>

            <label for="email">Email:</label><br>
            <input type="text" id="email" name="email" placeholder="exemplo@email.com"><br><br>

            <label for="telefone">Telefone:</label><br>
            <input type="text" id="telefone" name="telefone" placeholder="(xx) xxxx-xxxx"><br><br>

            <input type="submit" value="Cadastrar">

        </form>
        @endcan
    </div>
</div>
@endsection


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
    // Formato cnpj
    $('.cnpj').mask('00.000.000/0000-00', {
        reverse: true
    });
});
</script>
@endsection