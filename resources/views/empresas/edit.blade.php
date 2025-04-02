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
document.addEventListener('DOMContentLoaded', function() {
    // Seleciona o botão de adicionar
    let botaoAdicionar = document.getElementById('add-servico');

    // Verifica se o botão existe antes de adicionar o evento
    if (botaoAdicionar) {
        botaoAdicionar.addEventListener('click', function() {
            let container = document.getElementById('servicos-container');

            // Criar um novo div para o input e o botão de remover
            let novoCampo = document.createElement('div');
            novoCampo.classList.add('mb-3', 'd-flex', 'align-items-center');

            novoCampo.innerHTML = `
                    <div class="mr-1">
                    <select class="form-control select2" name="servico_id" id="servico_id">
                        <option value="" selected>Serviço...</option>
                        @foreach ($servicos as $servico)
                        <option value="{{ $servico->id }}">{{ $servico->descricao }} - {{ $servico->unidade_medida }}
                        </option>
                        @endforeach
                    </select>
                </div>
               
                    <button type="button" class="btn btn-danger remove-batata">
                        <i class="fas fa-minus"></i>
                    </button>
                `;

            // Adiciona o novo campo ao container
            container.appendChild(novoCampo);

            // Adiciona evento ao botão de remover dentro do novo campo
            novoCampo.querySelector('.remove-batata').addEventListener('click', function() {
                novoCampo.remove();
            });
        });
    }
});
</script>


@endsection