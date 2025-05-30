<!-- resources/views/licitacoes/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Criar Pedido
    </div>
    <div class="card-body">
        @can("admin-financeiro")
        @include('pedidos.partials.cabecalho')
        <form action="{{ route('pedidos.store') }}" method="POST">
            @csrf
            @include('pedidos.partials.forms')
        </form>
        @endcan
    </div>
</div>


@endsection

@section('javascripts_bottom')
@parent

<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Script de inicialização -->
<script>
$(document).ready(function() {
    $('.select2').select2();
});

function atualizarTabelaServicosSelecionados() {
    const selecionados = $('#servico_id').val(); // array de IDs selecionados
    let html = '';
    let somaTotal = 0;

    if (selecionados && selecionados.length > 0) {
        html += '<table class="table table-bordered">';
        html +=
            '<thead><tr><th>Empresa</th><th>Descrição</th><th>Unidade</th><th>Qtd</th><th>Valor Unitário</th><th>Valor Total</th><th></th></tr></thead><tbody>';

        selecionados.forEach(id => {
            const option = $('#servico_id').find('option[value="' + id + '"]');
            const empresa = option.data('empresa');
            const descricao = option.data('descricao');
            const unidade = option.data('unidade');
            const valor = parseFloat(option.data('valor').replace(',', '.')).toFixed(2);

            html += `<tr data-id="${id}">
                        <td>${empresa}</td>
                        <td>${descricao}</td>
                        <td>${unidade}</td>
                        <td>
                            <input type="number" class="form-control quantidade-input" min="0" value="0" data-id="${id}" />
                        </td>
                        <td>R$ ${valor.replace('.', ',')}</td>
                        <td class="valor-total" data-id="${id}">R$ 0,00</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger remover-servico" data-id="${id}">
                                &times;
                            </button>
                        </td>
                    </tr>`;
        });

        // Linha de total será adicionada depois do fechamento do loop

        html += `<tr style="background-color: #f8f9fa;">
                    <td colspan="4"></td>
                    <td><strong>Total do Pedido</strong></td>
                    <td id="soma-total" style="font-weight: bold;">R$ 0,00</td>
                    <td></td>
                 </tr>`;

        html += '</tbody></table>';
    } else {
        html = '<p>Nenhum serviço selecionado.</p>';
    }

    $('#tabela-servicos-selecionados').html(html);
}


$(document).on('input', '.quantidade-input', function() {
    let somaTotal = 0;

    $('.quantidade-input').each(function() {
        const id = $(this).data('id');
        const qtd = parseFloat($(this).val()) || 0;
        const option = $('#servico_id').find(`option[value="${id}"]`);
        const valorUnitario = parseFloat(option.data('valor').replace(',', '.')) || 0;
        const total = qtd * valorUnitario;

        $(`.valor-total[data-id="${id}"]`).text(`R$ ${total.toFixed(2).replace('.', ',')}`);
        somaTotal += total;
    });

    $('#soma-total').text(`R$ ${somaTotal.toFixed(2).replace('.', ',')}`);
});


$('#servico_id').on('change', function() {
    atualizarTabelaServicosSelecionados();
});

$('#modalServicos').on('hidden.bs.modal', function() {
    atualizarTabelaServicosSelecionados();
});

$(document).on('click', '.remover-servico', function() {
    const id = $(this).data('id');
    let selecionados = $('#servico_id').val(); // array de valores selecionados

    // Remove o ID da lista de selecionados
    selecionados = selecionados.filter(val => val != id);

    // Atualiza o select2
    $('#servico_id').val(selecionados).trigger('change');
});
</script>

@endsection