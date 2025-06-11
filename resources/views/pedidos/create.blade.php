<!-- resources/views/licitacoes/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Criar Pedido
    </div>

    <div class="card-body">
        @can("acesso-revista")
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Script de inicialização -->
<script>

function formatarMilhares(numero) {
    return numero.toFixed(2)
        .replace('.', ',')                    // vírgula para centavos
        .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // ponto a cada milhar
}

$(document).ready(function() {
    $('.select2').select2();
});

function atualizarTabelaServicosSelecionados() {
    const selecionados = $('#servico_id').val(); // array de IDs selecionados
    let html = '';
    let somaTotal = 0;

    if (selecionados && selecionados.length > 0) {
        html += '<table class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive mb-0">';
        html +=
            '<thead><tr><th>Empresa</th><th>Descrição</th><th>Unidade</th><th>Quantidade</th><th>Valor Unitário (R$)</th><th>Valor Total (R$)</th><th></th></tr></thead><tbody>';

        selecionados.forEach(id => {
            const option = $('#servico_id').find('option[value="' + id + '"]');
            const empresa = option.data('empresa');
            const descricao = option.data('descricao');
            const unidade = option.data('unidade');
            const valorBr = option.attr('data-valor') || '0,00';

            const valor = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;


            html += `<tr data-id="${id}">
                        <td>${empresa}</td>
                        <td>${descricao}</td>
                        <td>${unidade}</td>
                        <td>
                            <input type="text" name="quantidades[${id}]" class="form-control quantidade-input milhares-mask" min="0" value="0" data-id="${id}" />
                        </td>

                        <td class="text-right">${valor.toFixed(2).replace('.', ',')}</td>
                        <td class="valor-total text-right" data-id="${id}">0,00</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger remover-servico" data-id="1"><i class="fa fa-times" aria-hidden="true"></i></button>
                        </td>
                    </tr>`;
        });

        // Linha de total será adicionada depois do fechamento do loop

        html += `<tr style="background-color: #f8f9fa;">
                    <td colspan="4"></td>
                    <td><strong>Total do Pedido</strong></td>
                    <td id="soma-total" class="text-right" style="font-weight: bold;">0,00</td>
                    <td></td>
                 </tr>`;

        html += '</tbody></table>';
    } else {
        html = '<p>Nenhum serviço selecionado.</p>';
    }

    $('#tabela-servicos-selecionados').html(html);

    $('.milhares-mask').mask('000.000.000.000.000', {
    reverse: true
    });
    
}


$(document).on('input', '.quantidade-input', function() {
    let somaTotal = 0;

    $('.quantidade-input').each(function() {
        const id = $(this).data('id');
        const quantidade = parseFloat($(this).val().replace(/\./g, '').replace(',', '.')) || 0;
        const option = $('#servico_id').find(`option[value="${id}"]`);
        const valorBr = option.attr('data-valor') || '0,00';
        const valorUnitario = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;

        const total = quantidade * valorUnitario;

        $(`.valor-total[data-id="${id}"]`).text(formatarMilhares(total));
        somaTotal += total;
    });

    $('#soma-total').text(formatarMilhares(somaTotal));
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