@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Pedido
    </div>

    <div class="card-body">
        @can("acesso-revista")
        
        @include('pedidos.partials.cabecalho')
        
        @include('pedidos.partials.arquivos')
        
        {{-- O PARTIAL INTELIGENTE DE ARQUIVOS É INCLUÍDO AQUI. Ele contém o form#form-arquivos. --}}
        @include('pedidos.partials.add_arquivos', ['pedido' => $pedido]) 
        

        <div class="card mt-4 ml-2">
            <div class="card-header h5">
                Informações do Pedido
            </div>

            {{-- 1. Formulário Principal para Dados e Serviços (PUT) --}}
            <form action="{{ route('pedidos.update', $pedido->id) }}" method="POST" id="form-principal">
                @csrf
                @method('PUT')
                
                {{-- CORREÇÃO CRÍTICA DE MARGEM: Adicionando padding (p-4) para evitar que o conteúdo grude nas laterais do card. --}}
                <div class="p-4"> 
                    @include('pedidos.partials.forms')
                </div>
                
                {{-- Ajustando o padding para o botão final (pt-0 para evitar padding duplo com o mt-5 do forms) --}}
                <div class="mt-5 p-4 pt-0"> 
                    <button id="btn-salvar" type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar Edição</button>

                    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                        Voltar</a>
                </div>
            </form>
        </div>
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
function formatarValorBrasileiro(valor) {
    return valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Função auxiliar para formatar a quantidade (mantida para consistência)
function formatarMilhares(numero) {
    return Math.round(numero).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}
function formatarMilharesInteiro(numero) {
    return Math.round(numero).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

// NOVO: FUNÇÃO PARA CONTROLAR O BOTÃO DE SALVAR EDIÇÃO (APENAS SERVIÇOS/DADOS)
function controlarBotaoSalvar() {
    const hasQuantityError = $('.quantidade-input.is-invalid').length > 0;
    const hasIrregularServiceError = $('#aviso-irregular').is(':visible'); 
    const isAnyQuantityEmpty = $('.quantidade-input').filter(function() { return $(this).val() === ''; }).length > 0;
    
    // O botão Salvar Edição IGNORA a validação de arquivos.

    if (hasQuantityError || hasIrregularServiceError || isAnyQuantityEmpty) {
        $('#btn-salvar').prop('disabled', true);
    } else {
        $('#btn-salvar').prop('disabled', false);
    }
}

// NOVO: FUNÇÃO PARA CONTROLAR O BOTÃO DE ANEXAR ARQUIVOS
function controlarBotaoAnexar() {
    const hasFiles = $('#arquivos').get(0)?.files?.length > 0;
    // Checa se os campos de descrição gerados dinamicamente estão vazios.
    const hasEmptyDescription = $('#file-names-display input[name="descricoes[]"]').filter(function() { 
        return $(this).val().trim() === ''; 
    }).length > 0;
    
    const allFilled = hasFiles && !hasEmptyDescription;
    
    // Habilita/Desabilita o botão do formulário de upload
    $('#btn-anexar-arquivos').prop('disabled', !allFilled);
}


$(document).ready(function() {
    $('.select2').select2({language: "pt-BR"});
    atualizarTabelaServicosSelecionados(); // Carrega a tabela se já houver selecionados

    // --- Lógica para exibir nomes de múltiplos arquivos selecionados ---
    $('#arquivos').on('change', function() {
        const files = this.files;
        const $display = $('#file-names-display');
        $display.empty();

        if (files.length > 0) {
            let fileListHtml = `<p><strong>${files.length} arquivo(s) selecionado(s) para upload:</strong></p>
                <table class="table table-sm table-striped small mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Arquivo</th>
                            <th style="width: 15%;">Tamanho</th>
                            <th style="width: 45%;">Descrição (Obrigatória)</th> 
                        </tr>
                    </thead>
                    <tbody>`;

            for (let i = 0; i < files.length; i++) {
                const fileSizeMB = (files[i].size / 1024 / 1024).toFixed(2);
                
                fileListHtml += `<tr>
                    <td><i class="fas fa-file-alt mr-1"></i> ${files[i].name}</td>
                    <td>${fileSizeMB} MB</td>
                    {{-- Usando required para validação HTML --}}
                    <td><input type="text" name="descricoes[]" class="form-control form-control-sm" placeholder="Descreva o conteúdo..." required></td> 
                </tr>`;
            }

            fileListHtml += '</tbody></table>';
            $display.html(fileListHtml);
        }
        
        // Dispara a checagem do botão ANEXAR
        controlarBotaoAnexar(); 
    });
    // --- FIM DA LÓGICA DE ARQUIVOS ---
});

// Atualiza tabela com base nos serviços selecionados
function atualizarTabelaServicosSelecionados() {
    const selecionados = $('#servico_id').val(); // array de IDs selecionados
    let html = '';
    let somaTotal = 0;

    if (selecionados && selecionados.length > 0) {
        html += '<table class="table table-hover datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive mb-0">';
        html += '<thead><tr><th>Empresa</th><th>Descrição</th><th>Unidade</th><th>Quantidade Disponível</th><th>Quantidade</th><th>Valor Unitário R$</th><th>Valor Total R$</th><th></th></tr></thead><tbody>';

        selecionados.forEach(id => {
            const option = $('#servico_id').find('option[value="' + id + '"]');
            const empresa = option.data('empresa');
            const descricao = option.data('descricao');
            const unidade = option.data('unidade');
            const valorBr = option.attr('data-valor') || '0,00';
            const valor = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;

            const quantidadePivot = parseFloat(option.data('quantidade')) || 0;
            const quantidadeInicial = quantidadePivot; 
            
            // LÊ A QUANTIDADE DISPONÍVEL (AGORA FORNECIDA PELO CONTROLLER)
            const qtdDisponivel = option.data('quantidade-disponivel') || 0; 

            const valorTotal = quantidadeInicial * valor;


            html += `<tr data-id="${id}">
                        <td>${empresa}</td>
                        <td>${descricao}</td>
                        <td>${unidade}</td>
                        <td>${formatarMilharesInteiro(qtdDisponivel)}</td> <td>
                            <input type="text" name="quantidades[${id}]" class="form-control quantidade-input milhares-mask" value="${formatarMilhares(quantidadeInicial)}" data-id="${id}" />
                            <div class="quantidade-erro text-danger small mt-1" data-id="${id}" style="display:none;"></div>
                        </td>
                        <td class="text-right"> ${formatarValorBrasileiro(valor)}</td>
                        <td class="valor-total text-right" data-id="${id}">${formatarValorBrasileiro(valorTotal)}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger remover-servico" data-id="${id}"><i class="fa fa-times" aria-hidden="true"></i></button>
                        </td>
                    </tr>`;

            somaTotal += valorTotal;
        });

        html += `<tr style="background-color: #f8f9fa;">
                    <td colspan="4"></td>
                    <td class="text-right"><strong>Total do Pedido</strong></td>
                    <td id="soma-total" class="text-right" style="font-weight: bold;">${formatarValorBrasileiro(somaTotal)}</td>
                    <td></td>
                 </tr>`;

        html += '</tbody></table>';
    } else {
        html = '<p>Nenhum serviço selecionado.</p>';
    }

    $('#tabela-servicos-selecionados').html(html);

    // Reaplicar máscaras
    $('.milhares-mask').mask('000.000.000.000.000', {
        reverse: true
    });
}

// Quando altera a quantidade manualmente (ADICIONADA VALIDAÇÃO DE LIMITE)
$(document).on('input', '.quantidade-input', function() {
    let somaTotal = 0;
    
    // PREPARAÇÃO: Limpa a string de quantidade para cálculo
    const $input = $(this);
    const id = $input.data('id');
    const quantidadeUserStr = $input.val().replace(/\./g, ''); 
    const quantidadeUser = parseFloat(quantidadeUserStr.replace(',', '.')) || 0;
    
    const $errorDiv = $(`.quantidade-erro[data-id="${id}"]`);

    const option = $('#servico_id').find(`option[value="${id}"]`);
    const valorBr = option.attr('data-valor') || '0,00';
    const valorUnitario = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;
    
    // Pega o limite disponível, ou usa MAX_SAFE_INTEGER para desabilitar o check (interno)
    const quantidadeLimite = option.data('quantidade-disponivel') || Number.MAX_SAFE_INTEGER; 

    // --- VALIDAÇÃO DE LIMITE NO FRONTEND ---
    if (quantidadeUser > quantidadeLimite) {
        $input.addClass('is-invalid');
        const displayLimit = quantidadeLimite === Number.MAX_SAFE_INTEGER ? 'Não Definido' : formatarMilharesInteiro(quantidadeLimite);
        $errorDiv.text(`Quantidade Máxima: ${displayLimit}`).show();
    } else {
        $input.removeClass('is-invalid');
        $errorDiv.hide();
    }
    
    // CÁLCULO GERAL
    $('.quantidade-input').each(function() {
        const q = parseFloat($(this).val().replace(/\./g, '').replace(',', '.')) || 0;
        const lineOption = $('#servico_id').find(`option[value="${$(this).data('id')}"]`);
        const lineValorBr = lineOption.attr('data-valor') || '0,00';
        const lineValorUnitario = parseFloat(lineValorBr.replace('.', '').replace(',', '.')) || 0;
        
        somaTotal += (q * lineValorUnitario);
        
        // Atualiza valor total da linha atual
        if ($(this).data('id') === id) {
             $(`.valor-total[data-id="${id}"]`).text(formatarValorBrasileiro(q * lineValorUnitario));
        }
    });

    $('#soma-total').text(formatarValorBrasileiro(somaTotal));
    
    // Controla o botão salvar após todas as verificações
    controlarBotaoSalvar();
});


// Evento para monitorar as descrições de arquivos (AGORA CONTROLA O BOTÃO ANEXAR)
// O input name="descricoes[]" agora está DENTRO do form#form-arquivos na Edição.
$(document).on('input', '#form-arquivos input[name="descricoes[]"]', controlarBotaoAnexar);


$('#servico_id').on('change', function() {
    atualizarTabelaServicosSelecionados();
    controlarBotaoSalvar(); 
});

// Ao fechar o modal
$('#modalServicos').on('hidden.bs.modal', function() {
    atualizarTabelaServicosSelecionados();
});

// Remoção de serviço (remove da tabela e do select2)
$(document).on('click', '.remover-servico', function() {
    const id = $(this).data('id'); // ID lido do data-id do botão
    let selecionados = $('#servico_id').val() || [];

    selecionados = selecionados.filter(val => val != id);

    $('#servico_id').val(selecionados).trigger('change');
});

// No submit, limpa a máscara das quantidades antes de enviar
// Checa se o formulário é o principal antes de limpar a máscara
$('form').on('submit', function(e) {
    if ($(this).attr('id') === 'form-principal') {
        $('.quantidade-input').each(function() {
            let val = $(this).val();
            val = val.replace(/\./g, '');  // remove pontos de milhar
            $(this).val(val);
        });
    }
    // O form de arquivos (id="form-arquivos") não tem quantidades para limpar.
    // Se o submit for no form de arquivos, ele prossegue sem limpar.
});
</script>
@endsection