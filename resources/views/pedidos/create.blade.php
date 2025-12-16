@extends('layouts.app')

@section('content')


<div class="card">
    <div class="card-header h5">
        Criar Pedido
    </div>

    <div class="card-body">
        @can("acesso-revista")
        
        @include('pedidos.partials.cabecalho') 

        <form action="{{ route('pedidos.store') }}" method="POST" enctype="multipart/form-data"> 
            @csrf
            
            @include('pedidos.partials.forms')

            @include('pedidos.partials.add_arquivos')

            <div class="mt-5">
                <button id="btn-salvar" type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
                <a href="{{ route('pedidos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                    Voltar</a>
            </div>

        </form>
        @endcan
    </div>
</div>


@endsection

@section('javascripts_bottom')
@parent

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>

<script>
/**
 * Funções auxiliares para formatação de números.
 */

// Formata números monetários (duas casas decimais)
function formatarMilhares(numero) {
    return numero.toFixed(2)
        .replace('.', ',')                    // vírgula para centavos
        .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // ponto a cada milhar
}

// Formata números inteiros (sem casas decimais)
function formatarMilharesInteiro(numero) {
    return Math.round(numero)
        .toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}


$(document).ready(function() {
    $('.select2').select2({language: "pt-BR"});
    
    // --- Lógica para exibir nomes de múltiplos arquivos selecionados (melhoria de usabilidade) ---
    $('#arquivos').on('change', function() {
        const files = this.files;
        const $display = $('#file-names-display');
        $display.empty(); // Limpa a lista anterior

        if (files.length > 0) {
            
            // Gerando a estrutura de tabela para exibição de detalhes dos arquivos
            let fileListHtml = `<p><strong>${files.length} arquivo(s) selecionado(s):</strong></p>
                <table class="table table-sm table-striped small mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Arquivo</th>
                            <th style="width: 15%;">Tamanho</th>
                            <th style="width: 45%;">Descrição</th> 
                        </tr>
                    </thead>
                    <tbody>`;

            for (let i = 0; i < files.length; i++) {
                const fileSizeMB = (files[i].size / 1024 / 1024).toFixed(2);
                
                fileListHtml += `<tr>
                    <td><i class="fas fa-file-alt mr-1"></i> ${files[i].name}</td>
                    <td>${fileSizeMB} MB</td>
                    <td><input type="text" name="descricoes[]" class="form-control form-control-sm" placeholder="Obrigatório" required></td>
                </tr>`;
            }

            fileListHtml += '</tbody></table>';
            $display.html(fileListHtml);
        }
        
        // Dispara a checagem do botão salvar, pois os campos de descrição estão agora visíveis
        controlarBotaoSalvar();
    });
    // --- FIM DA LÓGICA DE ARQUIVOS ---
});

// Validação final de máscara no submit (mantido)
$('form').on('submit', function() {
    // Se o botão já estiver desabilitado por algum erro, impede o submit.
    if ($('#btn-salvar').prop('disabled')) {
        alert('Corrija os erros de quantidade ou serviços pendentes antes de salvar.');
        return false;
    }
    
    // LIMPEZA DE MÁSCARA: Limpa os pontos e vírgulas das quantidades para que o PHP receba números válidos.
    $('.quantidade-input').each(function() {
        const valorComMascara = $(this).val();
        const valorLimpo = valorComMascara.replace(/\./g, '').replace(',', '.');
        $(this).val(valorLimpo);
    });
});

// Validação de Serviço Irregular (mantido)
$('#servico_id').on('change', function() {
    let mostrarAviso = false;

    // Verifica se algum serviço selecionado não tem empresa associada
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

    // Chama a função de atualização da tabela e controle do botão
    atualizarTabelaServicosSelecionados();
    controlarBotaoSalvar();
});


function atualizarTabelaServicosSelecionados() {
    const selecionados = $('#servico_id').val(); // array de IDs selecionados
    let html = '';
    let somaTotal = 0;

    if (selecionados && selecionados.length > 0) {
        
        // 1. Abertura da DIV que cria o SCROLL LATERAL
        html += '<div class="table-responsive">';
        
        // 2. Tabela Limpa (Sem datatable-simples)
        html += '<table class="table table-hover table-striped table-sm mb-0">';
        
        // Cabeçalho com white-space: nowrap para não quebrar linha
        html += '<thead><tr>';
        html += '<th style="white-space: nowrap;">Empresa</th>';
        html += '<th style="white-space: nowrap;">Descrição</th>';
        html += '<th style="white-space: nowrap;">Unidade</th>';
        html += '<th class="text-right" style="white-space: nowrap;">Prazo (dias)</th>';
        html += '<th class="text-right" style="white-space: nowrap;">Qtd. Disp.</th>';
        html += '<th style="min-width: 100px;">Quantidade</th>'; // Largura mínima para o input
        html += '<th class="text-right" style="white-space: nowrap;">Valor Unit. (R$)</th>';
        html += '<th class="text-right" style="white-space: nowrap;">Valor Total (R$)</th>';
        html += '<th style="width: 50px;"></th>'; // Coluna do botão
        html += '</tr></thead><tbody>';

        selecionados.forEach(id => {
            const option = $('#servico_id').find('option[value="' + id + '"]');
            const empresa = option.data('empresa');
            const descricao = option.data('descricao');
            const unidade = option.data('unidade');
            const prazo = option.data('prazo');
            const valorBr = option.attr('data-valor') || '0,00';
            const qtdDisponivel = option.data('quantidade-disponivel') || 0; 
            const valor = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;

            const errorClass = (empresa == "Esse serviço não está associado a nenhuma empresa!") ? 'style="color: red; white-space: nowrap;"' : 'style="white-space: nowrap;"';
            
            html += 
            `<tr data-id="${id}">
                <td ${errorClass}>${empresa}</td>
                <td style="white-space: nowrap;">${descricao}</td>
                <td style="white-space: nowrap;">${unidade}</td>
                <td class="text-right">${prazo}</td>
                <td class="text-right">${formatarMilharesInteiro(qtdDisponivel)}</td> 
                <td>
                    <input type="text" name="quantidades[${id}]" class="form-control quantidade-input milhares-mask" data-id="${id}" style="min-width: 80px;" />
                    <div class="quantidade-erro text-danger small mt-1" data-id="${id}" style="display:none;"></div> 
                </td>

                <td class="text-right" style="white-space: nowrap;">${valor.toFixed(2).replace('.', ',')}</td>
                <td class="valor-total text-right" data-id="${id}" style="white-space: nowrap;">0,00</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remover-servico" data-id="${id}"><i class="fa fa-times" aria-hidden="true"></i></button>
                </td>
            </tr>`;
        });

        // Linha de total
        html += `<tr style="background-color: #f8f9fa;">
                    <td colspan="6"></td> 
                    <td style="white-space: nowrap;"><strong>Total do Pedido</strong></td>
                    <td id="soma-total" class="text-right" style="font-weight: bold; white-space: nowrap;">0,00</td>
                    <td></td>
                 </tr>`;

        html += '</tbody></table>';
        html += '</div>'; // Fecha a div table-responsive
    } else {
        html = '<p>Nenhum serviço selecionado.</p>';
    }

    $('#tabela-servicos-selecionados').html(html);

    // Reaplicar máscaras
    $('.milhares-mask').mask('000.000.000.000.000', { reverse: true });
}


// Função para controlar o estado do botão Salvar
function controlarBotaoSalvar() {
    const hasQuantityError = $('.quantidade-input.is-invalid').length > 0;
    const hasIrregularServiceError = $('#aviso-irregular').is(':visible');
    const isAnyQuantityEmpty = $('.quantidade-input').filter(function() { return $(this).val() === ''; }).length > 0;
    
    // NOVO CHECK: Verifica se alguma descrição de arquivo está vazia
    const hasEmptyFileDescription = $('input[name="descricoes[]"]').filter(function() { return $(this).val().trim() === ''; }).length > 0;

    // Desabilita se qualquer erro ou campo vazio (incluindo descrição de arquivo) for encontrado
    if (hasQuantityError || hasIrregularServiceError || isAnyQuantityEmpty || hasEmptyFileDescription) {
        $('#btn-salvar').prop('disabled', true);
    } else {
        $('#btn-salvar').prop('disabled', false);
    }
}


// Lógica principal de cálculo e validação em tempo real
$(document).on('input', '.quantidade-input', function() {
    let somaTotal = 0;
    
    // PREPARAÇÃO: Limpa a string de quantidade para cálculo
    const quantidadeUserStr = $(this).val().replace(/\./g, ''); 
    const quantidadeUser = parseFloat(quantidadeUserStr.replace(',', '.')) || 0;
    
    const id = $(this).data('id');
    const $input = $(this);
    const $errorDiv = $(`.quantidade-erro[data-id="${id}"]`);

    const option = $('#servico_id').find(`option[value="${id}"]`);
    const valorBr = option.attr('data-valor') || '0,00';
    const valorUnitario = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;
    // Pega o limite disponível do data-attribute
    const quantidadeLimite = option.data('quantidade-disponivel') || 0;

    // --- VALIDAÇÃO DE LIMITE NO FRONTEND ---
    if (quantidadeUser > quantidadeLimite) {
        $input.addClass('is-invalid');
        $errorDiv.text(`Quantidade Máxima: ${formatarMilharesInteiro(quantidadeLimite)}`).show();
    } else {
        $input.removeClass('is-invalid');
        $errorDiv.hide();
    }
    
    // CÁLCULO DA LINHA
    const total = quantidadeUser * valorUnitario;
    $(`.valor-total[data-id="${id}"]`).text(formatarMilhares(total));

    // CÁLCULO GERAL
    $('.quantidade-input').each(function() {
        const q = parseFloat($(this).val().replace(/\./g, '').replace(',', '.')) || 0;
        const lineId = $(this).data('id');
        const lineOption = $('#servico_id').find(`option[value="${lineId}"]`);
        const lineValorBr = lineOption.attr('data-valor') || '0,00';
        const lineValorUnitario = parseFloat(lineValorBr.replace('.', '').replace(',', '.')) || 0;
        
        somaTotal += (q * lineValorUnitario);
    });

    $('#soma-total').text(formatarMilhares(somaTotal));
    
    // Controla o botão salvar após todas as verificações
    controlarBotaoSalvar();
});


// Evento para monitorar as descrições de arquivos (para o botão salvar)
$(document).on('input', 'input[name="descricoes[]"]', controlarBotaoSalvar);


$('#servico_id').on('change', function() {
    atualizarTabelaServicosSelecionados();
    controlarBotaoSalvar(); 
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
