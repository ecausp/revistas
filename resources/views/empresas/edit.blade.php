<!-- resources/views/empresas/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Empresa
    </div>
    <div class="card-body">

        @can('admin-revista')
        <form action="{{ route('empresas.update', $empresa) }}" method="POST">
            @csrf
            @method('PUT')
            @include('empresas.partials.forms')
        </form>
    </div>
    @endcan
</div>

<!-- Modal precisa ficar aqui para evitar erros de carregamento atrasado do DOM -->
<div class="modal fade" id="servicoModal" tabindex="-1" aria-labelledby="servicoModalLabel" aria-hidden="true"
    data-bs-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="card">
                <div class="modal-header card-header h5">
                    Criar Novo Serviço
                </div>
                <div class="modal-body card-body">
                    <form id="novoServicoForm" action="{{ route('servicos.store') }}" method="POST">
                        @csrf
                        <div class="mb-1">
                            <label for="descricao" class="form-label">Descrição</label>
                            <input type="text" class="form-control" name="descricao" id="descricao"
                                placeholder="Descrição do Serviço..." required>
                        </div>
                        <div class="mb-1">
                            <label for="valor_unitario" class="form-label">Valor Unitário</label>
                            <input type="text" class="form-control money" name="valor_unitario" id="valor_unitario"
                                placeholder="Valor Unitário" required>
                        </div>
                        <div class="mb-1">
                            <label for="unidade_medida" class="form-label">Unidade de Medida</label>
                            <select class="form-control select2" name="unidade_medida" id="unidade_medida" required>
                                <option value="" selected>Selecione Unidade de Medida...</option>
                                @foreach ($unidades_medida as $unidade)
                                <option value="{{ $unidade }}">{{ $unidade }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
$(document).ready(function() {
    // Formato cnpj
    $('.cnpj').mask('00.000.000/0000-00', {
        reverse: true
    });
});
</script>

<script>
// exibe o modal
$(document).ready(function() {
    const modal = new bootstrap.Modal(document.getElementById('servicoModal'));

    $('#servico-modal').on('click', function() {
        modal.show();
    });
});
</script>

<script>
$(document).ready(function() {
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

<script>
$(document).ready(function() {
    // evita o envio normal do form, salvando no banco de dados sem redirecionar para outra página;
    $('.select2').select2();

    $('#novoServicoForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);
        var actionUrl = form.attr('action');

        $.ajax({
            url: actionUrl,
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                console.log('Resposta do servidor:', response);

                const newOption = new Option(
                    `${response.descricao} - ${response.unidade_medida}`,
                    response.id,
                    true,
                    true
                );

                $('#servico_id').append(newOption).trigger('change');
                $('#servicoModal').modal('hide');
                $('#novoServicoForm')[0].reset();
            },
            error: function(xhr) {
                alert('Erro ao cadastrar o serviço');
                console.log(xhr.responseText);
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

@endsection