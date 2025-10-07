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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>

<script>

function formatarMilhares(numero) {
    return numero.toFixed(2)
        .replace('.', ',')                    // vírgula para centavos
        .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // ponto a cada milhar
}

// Função auxiliar para formatar números inteiros (sem centavos) para exibição
function formatarMilharesInteiro(numero) {
    return Math.round(numero)
        .toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}


$(document).ready(function() {
    $('.select2').select2({language: "pt-BR"});
});

// Validação final de máscara no submit (mantido)
$('form').on('submit', function() {
    // Se o botão já estiver desabilitado por algum erro, impede o submit
    if ($('#btn-salvar').prop('disabled')) {
        alert('Corrija os erros de quantidade ou serviços pendentes antes de salvar.');
        return false;
    }
    
    // Limpeza da máscara
    $('.quantidade-input').each(function() {
        const valorComMascara = $(this).val();
        const valorLimpo = valorComMascara.replace(/\./g, '').replace(',', '.');
        $(this).val(valorLimpo);
    });
});

// Validação de Serviço Irregular (mantido)
$('#servico_id').on('change', function() {
    let mostrarAviso = false;

    $(this).find('option:selected').each(function() {
        let empresa = $(this).data('empresa');
        if (empresa.includes("não está associado")) {
            mostrarAviso = true;
        }
    });

    if (mostrarAviso) {
        $('#aviso-irregular').show();
    } else {
        $('#aviso-irregular').hide();
    }

    // Chama a função de atualização e controle do botão
    atualizarTabelaServicosSelecionados();
    controlarBotaoSalvar();
});


function atualizarTabelaServicosSelecionados() {
    const selecionados = $('#servico_id').val(); // array de IDs selecionados
    let html = '';
    let somaTotal = 0;

    if (selecionados && selecionados.length > 0) {
        html += '<table class="table table-hover datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive mb-0">';
        
        // ORDEM DAS COLUNAS SOLICITADA:
        html +=
            '<thead><tr><th>Empresa</th><th>Descrição</th><th>Unidade</th><th>Quantidade Disponível</th><th>Quantidade</th><th>Valor Unitário (R$)</th><th>Valor Total (R$)</th><th></th></tr></thead><tbody>';

        selecionados.forEach(id => {
            const option = $('#servico_id').find('option[value="' + id + '"]');
            const empresa = option.data('empresa');
            const descricao = option.data('descricao');
            const unidade = option.data('unidade');
            const valorBr = option.attr('data-valor') || '0,00';
            const qtdDisponivel = option.data('quantidade-disponivel') || 0; 

            const valor = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;

            const errorClass = (empresa == "Esse serviço não está associado a nenhuma empresa!") ? 'style="color: red;"' : '';
            
            html += 
            `<tr data-id="${id}">
                <td ${errorClass}>${empresa}</td>
                <td>${descricao}</td>
                <td>${unidade}</td>
                <td>${formatarMilharesInteiro(qtdDisponivel)}</td> 
                <td>
                    <input type="text" name="quantidades[${id}]" class="form-control quantidade-input milhares-mask" data-id="${id}" />
                    <div class="quantidade-erro text-danger small mt-1" data-id="${id}" style="display:none;"></div> </td>

                <td class="text-right">${valor.toFixed(2).replace('.', ',')}</td>
                <td class="valor-total text-right" data-id="${id}">0,00</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remover-servico" data-id="${id}"><i class="fa fa-times" aria-hidden="true"></i></button>
                </td>
            </tr>`;
        });

        // Linha de total
        html += `<tr style="background-color: #f8f9fa;">
                    <td colspan="5"></td> 
                    <td><strong>Total do Pedido</strong></td>
                    <td id="soma-total" class="text-right" style="font-weight: bold;">0,00</td>
                    <td></td>
                 </tr>`;

        html += '</tbody></table>';
    } else {
        html = '<p>Nenhum serviço selecionado.</p>';
    }

    $('#tabela-servicos-selecionados').html(html);

    // Reaplicar máscaras após recriar o HTML
    $('.milhares-mask').mask('000.000.000.000.000', {
    reverse: true
    });
    
}


// NOVO: Função para controlar o estado do botão Salvar
function controlarBotaoSalvar() {
    const hasQuantityError = $('.quantidade-input.is-invalid').length > 0;
    const hasIrregularServiceError = $('#aviso-irregular').is(':visible');
    const isAnyQuantityEmpty = $('.quantidade-input').filter(function() { return $(this).val() === ''; }).length > 0;
    
    // Desabilita se houver erro de quantidade, erro de serviço irregular OU se algum campo de quantidade estiver vazio
    if (hasQuantityError || hasIrregularServiceError || isAnyQuantityEmpty) {
        $('#btn-salvar').prop('disabled', true);
    } else {
        $('#btn-salvar').prop('disabled', false);
    }
}


// ALTERADO: Adicionado validação de limite no evento de input
$(document).on('input', '.quantidade-input', function() {
    let somaTotal = 0;
    let hasError = false;

    // Remove pontos de milhar para garantir o parse correto do valor do input
    const quantidadeUserStr = $(this).val().replace(/\./g, ''); 
    const quantidadeUser = parseFloat(quantidadeUserStr.replace(',', '.')) || 0;
    
    const id = $(this).data('id');
    const $input = $(this);
    const $errorDiv = $(`.quantidade-erro[data-id="${id}"]`);

    const option = $('#servico_id').find(`option[value="${id}"]`);
    const valorBr = option.attr('data-valor') || '0,00';
    const valorUnitario = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;
    const quantidadeLimite = option.data('quantidade-disponivel') || 0;

    // --- VALIDAÇÃO DE LIMITE NO FRONTEND ---
    if (quantidadeUser > quantidadeLimite) {
        hasError = true;
        $input.addClass('is-invalid');
        $errorDiv.text(`Quantidade Máxima: ${formatarMilharesInteiro(quantidadeLimite)}`).show();
    } else {
        $input.removeClass('is-invalid');
        $errorDiv.hide();
    }
    
    // Recalcula o total para a linha atual (independente de erro)
    const total = quantidadeUser * valorUnitario;
    $(`.valor-total[data-id="${id}"]`).text(formatarMilhares(total));

    // Recalcula o Total Geral
    $('.quantidade-input').each(function() {
        const q = parseFloat($(this).val().replace(/\./g, '').replace(',', '.')) || 0;
        const lineId = $(this).data('id');
        const lineOption = $('#servico_id').find(`option[value="${lineId}"]`);
        const lineValorBr = lineOption.attr('data-valor') || '0,00';
        const lineValorUnitario = parseFloat(lineValorBr.replace('.', '').replace(',', '.')) || 0;
        
        somaTotal += (q * lineValorUnitario);
    });

    $('#soma-total').text(formatarMilhares(somaTotal));
    
    // Controla o botão salvar
    controlarBotaoSalvar();
});


$('#servico_id').on('change', function() {
    atualizarTabelaServicosSelecionados();
    controlarBotaoSalvar(); // Garante que o botão seja reavaliado após adicionar/remover serviços
});

$('#modalServicos').on('hidden.bs.modal', function() {
    atualizarTabelaServicosSelecionados();
});

$(document).on('click', '.remover-servico', function() {
    const id = $(this).closest('tr').data('id'); 
    let selecionados = $('#servico_id').val(); 

    selecionados = selecionados.filter(val => val != id);

    $('#servico_id').val(selecionados).trigger('change');
});

</script>

@endsection