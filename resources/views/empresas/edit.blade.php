<!-- resources/views/empresas/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Empresa
    </div>
    <div class="card-body">

        @can('admin')
        <form action="{{ route('empresas.update', $empresa) }}" method="POST">
            @csrf
            @method('PUT')
            @include('empresas.partials.forms')
        </form>
    </div>
    @endcan
</div>
@endsection



@section('styles')
@parent
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('javascripts_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function() {
    // Formato cnpj
    $('.cnpj').mask('00.000.000/0000-00', {
        reverse: true
    });
});
</script>
<script>
$(document).ready(function() {
    // Ativar Select2 nos selects que já existem
    $('.servico-select').select2({
        placeholder: "Selecione um serviço",
        allowClear: true
    });

    $('#add-servico').on('click', function() {
        let container = $('#servicos-container');

        // Criar um novo div para o select e botão de remover
        let novoCampo = $(`
<div class="mb-3 d-flex align-items-center">
                <div class="mr-1">
                    <select class="form-select servico-select" name="servico_id" id="servico_id">
                        <option value="" selected>Serviço...</option>
                        @foreach ($servicos as $servico)
                        <option value="{{ $servico->id }}">{{ $servico->descricao }} - {{ $servico->unidade_medida }}
                        </option>
                        @endforeach
                    </select>
                </div>
                    <button type="button" class="btn btn-danger remove-servico">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            `);

        // Adicionar o novo campo ao container
        container.append(novoCampo);

        // Aplicar Select2 no novo select
        novoCampo.find('.servico-select').select2({
            placeholder: "Selecione um serviço",
            allowClear: true
        });

        // Evento para remover o select
        novoCampo.find('.remove-servico').on('click', function() {
            novoCampo.remove();
        });
    });
});
</script>



@endsection