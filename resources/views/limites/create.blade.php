@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Criar limite
    </div>
    <div class="card-body">
        @can("admin-financeiro")
        <form action="{{ route('limites.store') }}" method="POST">
            @csrf
            @include('limites.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection

@section('styles')
@parent
<style>
.valor-negativo { color: #dc3545 !important; }
.valor-positivo { color: #28a745 !important; }
.valor-zero { color: #6c757d !important; }

.valor-disponivel {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
}

.valor-disponivel h6 {
    font-weight: 600;
    margin-bottom: 15px;
    color: #495057;
}

.linha-valor {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.total-disponivel {
    border-top: 1px dashed #dee2e6;
    padding-top: 10px;
    margin-top: 10px;
    font-weight: bold;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('javascripts_bottom')
@parent
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>

<script>
$(document).ready(function() {

    // ---------------------------
    // Inicializa Select2
    // ---------------------------
    $('.select2').select2({ language: "pt-BR" });

    // URLs do Laravel via route()
    const urlValorRecebido = "{{ route('saldos.valor-recebido') }}";
    const urlValorUnitario = "{{ url('/servicos') }}/"; // acrescenta ID depois
    const urlValorTotalLicitacao = "{{ url('/licitacoes') }}/"; // acrescenta ID depois + /valor-total

    // ---------------------------
    // Funções AJAX e cálculos
    // ---------------------------

    function carregarValorRecursoFinanceiro() {
        let licitacaoId = $('#licitacao_id').val();
        let revistaId = $('#revista_id').val();
        console.log("carregarValorRecursoFinanceiro chamado", { licitacaoId, revistaId });

        if (licitacaoId && revistaId) {
            $.get(urlValorRecebido, { licitacao_id: licitacaoId, revista_id: revistaId })
            .done(function(data) {
                console.log("Resposta AJAX /saldos/valor-recebido:", data);
                let valor = parseFloat(data.valor_recurso_financeiro) || 0;
                $('#valor_recurso_financeiro').text(valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            })
            .fail(function(jqXHR) {
                console.error("Erro AJAX /saldos/valor-recebido:", jqXHR);
                $('#valor_recurso_financeiro').text('0,00');
            });
        } else {
            $('#valor_recurso_financeiro').text('0,00');
        }
    }

    function carregarValorUnitario(servicoId) {
        if (servicoId) {
            $.get(urlValorUnitario + servicoId + "/valor-unitario")
            .done(function(data) {
                console.log("Resposta AJAX /servicos/valor-unitario:", data);
                let valor = parseFloat(data.valor_unitario) || 0;
                $('#valor_unitario').val(valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })).prop('readonly', true);
                atualizarValorTotal();
            })
            .fail(function(jqXHR) {
                console.error("Erro AJAX /servicos/valor-unitario:", jqXHR);
                $('#valor_unitario').val('0,00');
                atualizarValorTotal();
            });
        } else {
            $('#valor_unitario').val('0,00');
            atualizarValorTotal();
        }
    }

    function carregarValorLicitacao() {
        let licitacaoId = $('#licitacao_id').val();
        if (licitacaoId) {
            $.get(urlValorTotalLicitacao + licitacaoId + "/valor-total")
            .done(function(data) {
                console.log("Resposta AJAX /licitacoes/valor-total:", data);
                let valor = parseFloat(data.valor_total) || 0;
                $('#valor_total_licitacao').text(valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                calcularDiferenca();
            })
            .fail(function(jqXHR) {
                console.error("Erro AJAX /licitacoes/valor-total:", jqXHR);
                $('#valor_total_licitacao').text('0,00');
                calcularDiferenca();
            });
        } else {
            $('#valor_total_licitacao').text('0,00');
            calcularDiferenca();
        }
    }

    function atualizarValorTotal() {
        let quantidade = parseFloat($('#quantidade').val().replace(/\./g,'').replace(',','.')) || 0;
        let valorUnitario = parseFloat($('#valor_unitario').val().replace(/\./g,'').replace(',','.')) || 0;
        let valorTotal = valorUnitario * quantidade;
        $('#valor_total_pedido').text(valorTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        console.log("atualizarValorTotal chamado. Quantidade:", quantidade, "Valor unitário:", valorUnitario, "Valor total calculado:", valorTotal);
        calcularDiferenca();
    }

    function calcularDiferenca() {
        let valorLicitacao = parseFloat($('#valor_total_licitacao').text().replace(/\./g,'').replace(',','.')) || 0;
        let valorPedido = parseFloat($('#valor_total_pedido').text().replace(/\./g,'').replace(',','.')) || 0;
        let diferenca = valorLicitacao - valorPedido;

        $('#diferenca_valores').text(diferenca.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#diferenca_valores').removeClass('valor-positivo valor-negativo valor-zero');

        if (diferenca > 0) $('#diferenca_valores').addClass('valor-positivo');
        else if (diferenca < 0) $('#diferenca_valores').addClass('valor-negativo');
        else $('#diferenca_valores').addClass('valor-zero');

        console.log("calcularDiferenca chamado. Valor licitação:", valorLicitacao, "Valor pedido:", valorPedido, "Diferença:", diferenca);
    }

    // ---------------------------
    // Eventos
    // ---------------------------
    $('#licitacao_id, #revista_id').on('change', function() {
        carregarValorRecursoFinanceiro();
        carregarValorLicitacao();
    });

    $('#servico_id').on('change', function() {
        carregarValorUnitario($(this).val());
    });

    $('#quantidade, #valor_unitario').on('input', atualizarValorTotal);

    // ---------------------------
    // Máscaras monetárias
    // ---------------------------
    $('.money').mask('000.000.000.000.000,00', { reverse: true });
    $('#quantidade').mask('000.000.000.000', { reverse: true });

    // ---------------------------
    // Inicializa valores ao carregar a página
    // ---------------------------
    carregarValorRecursoFinanceiro();
    carregarValorLicitacao();
    if ($('#servico_id').val()) carregarValorUnitario($('#servico_id').val());

    // ---------------------------
    // Antes de submeter o formulário
    // ---------------------------
    $('form').on('submit', function() {
        // converte quantidade
        let quantidade = $('#quantidade').val();
        if (quantidade) $('#quantidade').val(quantidade.replace(/\./g,''));

        // converte valor_unitario
        let valorUnitario = $('#valor_unitario').val();
        if (valorUnitario) $('#valor_unitario').val(valorUnitario.replace(/\./g,'').replace(',','.'));

        // valida diferença
        let diferenca = parseFloat($('#diferenca_valores').text().replace(/\./g,'').replace(',','.')) || 0;
        if (diferenca < 0) {
            alert('Atenção! O valor total do pedido excede o valor da licitação.');
            return false;
        }
    });

});
</script>
@endsection


