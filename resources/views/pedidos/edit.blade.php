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

        <div class="card mt-4 ml-2">
                <div class="card-header h5">
        Informações do Pedido
                </div>

        <form action="{{ route('pedidos.update', $pedido->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="md-6 mb-2">
                <div class="mb-4">
                    <label for="servico" class="form-label mt-4">Serviços</label>

                    <select class="form-control select2" id="servico_id" name="servicos[]" multiple="multiple">
                        @foreach ($servicos as $servico)
                            @php
                                // Obtém a quantidade do serviço no pedido, se existir
                                $quantidade = $pedido->servicos->find($servico->id)?->pivot->quantidade ?? 0;
                            @endphp

                            <option 
                                value="{{ $servico->id }}" 
                                data-empresa="{{ $servico->empresas->first()->razao_social ?? '' }}"
                                data-descricao="{{ $servico->descricao }}" 
                                data-unidade="{{ $servico->unidade_medida }}"
                                data-valor="{{ number_format($servico->valor_unitario, 2, ',', '.') }}"
                                data-quantidade="{{ $quantidade }}"
                                @if ($pedido->servicos->contains($servico->id)) selected @endif>
                                {{ $servico->descricao }} - {{ $servico->unidade_medida }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div id="tabela-servicos-selecionados" class="mt-3"></div>

                <div class="mb-1">
                    <label for="observacoes">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="3">{{ old('observacoes', $pedido->observacoes) }}</textarea>
                </div>

                <div class="mb-1">
                    <input type="hidden" class="form-control" name="revista_id" id="revista_id" value="{{ $pedido->solicitante->revista->id }}">
                </div>

                <div class="mb-1">
                    <input type="hidden" class="form-control" name="solicitante_id" id="solicitante_id" value="{{ $pedido->solicitante->id }}">
                </div>

                <div class="mb-1">
                    <input type="hidden" class="form-control" name="licitacao_id" id="licitacao_id" value="{{ $pedido->licitacao->id }}">
                </div>
            </div>
            
            @include('pedidos.partials.add_arquivos')

            <div class="mt-5">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>

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
                    <td><input type="text" name="descricoes[]" class="form-control form-control-sm" placeholder="Descreva o conteúdo..." required></td>
                </tr>`;
            }

            fileListHtml += '</tbody></table>';
            $display.html(fileListHtml);
        }
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
        html += '<thead><tr><th>Empresa</th><th>Descrição</th><th>Unidade</th><th>Quantidade</th><th>Valor Unitário R$</th><th>Valor Total R$</th><th></th></tr></thead><tbody>';

        selecionados.forEach(id => {
            const option = $('#servico_id').find('option[value="' + id + '"]');
            const empresa = option.data('empresa');
            const descricao = option.data('descricao');
            const unidade = option.data('unidade');
            const valorBr = option.attr('data-valor') || '0,00';
            const valor = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;

            const quantidade = parseFloat(option.data('quantidade')) || 0;
            const valorTotal = quantidade * valor;


            html += `<tr data-id="${id}">
                        <td>${empresa}</td>
                        <td>${descricao}</td>
                        <td>${unidade}</td>
                        <td>
                            <input type="text" name="quantidades[${id}]" class="form-control quantidade-input milhares-mask" value="${formatarMilhares(quantidade)}" data-id="${id}" />
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

// Quando altera a quantidade manualmente (inclui lógica de total)
$(document).on('input', '.quantidade-input', function() {
    let somaTotal = 0;

    $('.quantidade-input').each(function() {
        const id = $(this).data('id');

        let quantidadeBr = $(this).val() || '0';
        quantidadeBr = quantidadeBr.replace(/\./g, '').replace(',', '.');
        const quantidade = parseFloat(quantidadeBr) || 0;
        
        const option = $('#servico_id').find('option[value="' + id + '"]');
        const valorBr = option.attr('data-valor') || '0,00';
        const valorUnitario = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;

        const total = quantidade * valorUnitario;

        // Atualiza valor total da linha
        $(`.valor-total[data-id="${id}"]`).text(formatarValorBrasileiro(total));

        somaTotal += total;
    });

    // Atualiza valor total do pedido
    $('#soma-total').text(formatarValorBrasileiro(somaTotal));
});

// Ao trocar o select2 (recarrega a tabela)
$('#servico_id').on('change', function() {
    atualizarTabelaServicosSelecionados();
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
$('form').on('submit', function() {
    $('.quantidade-input').each(function() {
        let val = $(this).val();
        val = val.replace(/\./g, '');  // remove pontos de milhar
        $(this).val(val);
    });
});
</script>
@endsection