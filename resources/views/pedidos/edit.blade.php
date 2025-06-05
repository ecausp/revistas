<!-- resources/views/licitacoes/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Pedido
    </div>

    <div class="card-body">
        @can("revista")
        
        @include('pedidos.partials.cabecalho')

        <form action="{{ route('pedidos.update', $pedido->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="md-6">
                <div class="mb-1">
                    <label for="servico" class="form-label">Serviços</label>

                    <select class="form-control select2" id="servico_id" name="servicos[]" multiple="multiple">
                        @foreach ($servicos as $servico)
                            @php
                                $quantidade = $pedido->servicos->find($servico->id)?->pivot->quantidade ?? 0;
                            @endphp

                            <option 
                                value="{{ $servico->id }}" 
                                data-empresa="{{ $servico->empresa->nome ?? '' }}"
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

                <div class="mt-5">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>

                    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                        Voltar</a>
                </div>
            </div>
        </form>
        @endcan
    </div>
</div>


@endsection


@section('javascripts_bottom')
@parent

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Bundle (JS + Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2();
    atualizarTabelaServicosSelecionados(); // Carrega a tabela se já houver selecionados
});

// Atualiza tabela com base nos serviços selecionados
function atualizarTabelaServicosSelecionados() {
    const selecionados = $('#servico_id').val(); // array de IDs selecionados
    let html = '';
    let somaTotal = 0;

    if (selecionados && selecionados.length > 0) {
        html += '<table class="table table-bordered">';
        html += '<thead><tr><th>Empresa</th><th>Descrição</th><th>Unidade</th><th>Quantidade</th><th>Valor Unitário</th><th>Valor Total</th><th></th></tr></thead><tbody>';

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
                            <input type="number" name="quantidades[${id}]" class="form-control quantidade-input" min="0" value="${quantidade}" data-id="${id}" />
                        </td>
                        <td>R$ ${valor.toFixed(2).replace('.', ',')}</td>
                        <td class="valor-total" data-id="${id}">R$ ${valorTotal.toFixed(2).replace('.', ',')}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-danger remover-servico" data-id="${id}">&times;</button>
                        </td>
                    </tr>`;

            somaTotal += valorTotal;
        });

        html += `<tr style="background-color: #f8f9fa;">
                    <td colspan="4"></td>
                    <td><strong>Total do Pedido</strong></td>
                    <td id="soma-total" style="font-weight: bold;">R$ ${somaTotal.toFixed(2).replace('.', ',')}</td>
                    <td></td>
                 </tr>`;

        html += '</tbody></table>';
    } else {
        html = '<p>Nenhum serviço selecionado.</p>';
    }

    $('#tabela-servicos-selecionados').html(html);
}

// Quando altera a quantidade manualmente
$(document).on('input', '.quantidade-input', function() {
    let somaTotal = 0;

    $('.quantidade-input').each(function() {
        const id = $(this).data('id');
        const quantidade = parseFloat($(this).val()) || 0;
        const option = $('#servico_id').find(`option[value="${id}"]`);
        const valorBr = option.attr('data-valor') || '0,00';
        const valorUnitario = parseFloat(valorBr.replace('.', '').replace(',', '.')) || 0;

        const total = quantidade * valorUnitario;
        $(`.valor-total[data-id="${id}"]`).text(`R$ ${total.toFixed(2).replace('.', ',')}`);
        somaTotal += total;
    });

    $('#soma-total').text(`R$ ${somaTotal.toFixed(2).replace('.', ',')}`);
});

// Ao trocar o select2
$('#servico_id').on('change', function() {
    atualizarTabelaServicosSelecionados();
});

// Ao fechar o modal
$('#modalServicos').on('hidden.bs.modal', function() {
    atualizarTabelaServicosSelecionados();
});

// Remoção de serviço
$(document).on('click', '.remover-servico', function() {
    const id = $(this).data('id');
    let selecionados = $('#servico_id').val() || [];

    selecionados = selecionados.filter(val => val != id);

    $('#servico_id').val(selecionados).trigger('change');
});
</script>
@endsection
