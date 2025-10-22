@extends('layouts.app')

@section('content')

<div id="alert-container"></div>

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
<!-- jQuery é obrigatório para Select2, Datepicker e para o seu script principal ($). 
     Deve ser carregado primeiro. -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawR/U0ldPzK4dSQW0uM44fQk86F/t7jF/SjU=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>
<script>
$(document).ready(function() {

    // ---------------------------
    // Inicializa Select2
    // ---------------------------
    $('.select2').select2({ language: "pt-BR" });

    // Desabilita o select se já existe limite (readonly)
    @if(isset($limite))
        $('#revista_id').select2({ language: "pt-BR" }).prop('disabled', true);
    @endif

    // URLs do Laravel via route()
    const urlValorRecebido = "{{ route('saldos.valor-recebido') }}";
    const urlValorUnitario = "{{ url('/servicos') }}/"; // acrescenta ID depois
    const urlValorTotalLicitacao = "{{ url('/licitacoes') }}/"; // acrescenta ID depois + /valor-total
    const urlTotalLimites = "{{ route('limites.total') }}"; // total de limites cadastrados

    // ---------------------------
    // Função para pegar o ID da revista corretamente
    // ---------------------------
    function getRevistaId() {
        let select = $('#revista_id');
        if (select.is(':disabled')) {
            return $('input[name="revista_id"]').val();
        }
        return select.val();
    }

    // ---------------------------
    // Funções AJAX e cálculos
    // ---------------------------
    function carregarValorRecursoFinanceiro() {
        let licitacaoId = $('#licitacao_id').val();
        let revistaId = getRevistaId();

        if (licitacaoId && revistaId) {
            $.get(urlValorRecebido, { licitacao_id: licitacaoId, revista_id: revistaId })
            .done(function(data) {
                let valor = parseFloat(data.valor_recurso_financeiro) || 0;
                $('#valor_recurso_financeiro').text(valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                carregarTotalLimites(); // Recarrega total de limites cadastrados
            })
            .fail(function() {
                $('#valor_recurso_financeiro').text('0,00');
                carregarTotalLimites();
            });
        } else {
            $('#valor_recurso_financeiro').text('0,00');
            carregarTotalLimites();
        }
    }

    function carregarTotalLimites() {
        let licitacaoId = $('#licitacao_id').val();
        let revistaId = getRevistaId();

        if (licitacaoId && revistaId) {
            $.get(urlTotalLimites, { licitacao_id: licitacaoId, revista_id: revistaId })
            .done(function(data) {
                let total = parseFloat(data.total_limites) || 0;
                $('#valor_limites_cadastrados').text(total.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                calcularDiferenca();
            })
            .fail(function() {
                $('#valor_limites_cadastrados').text('777');
                calcularDiferenca();
            });
        } else {
            $('#valor_limites_cadastrados').text('666');
            calcularDiferenca();
        }
    }

    function carregarValorUnitario(servicoId) {
        if (servicoId) {
            $.get(urlValorUnitario + servicoId + "/valor-unitario")
            .done(function(data) {
                let valor = parseFloat(data.valor_unitario) || 0;
                $('#valor_unitario').val(valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })).prop('readonly', true);
                atualizarValorTotal();
            })
            .fail(function() {
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
                let valor = parseFloat(data.valor_total) || 0;
                $('#valor_total_licitacao').text(valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            })
            .fail(function() {
                $('#valor_total_licitacao').text('0,00');
            });
        } else {
            $('#valor_total_licitacao').text('0,00');
        }
    }

    function atualizarValorTotal() {
        let quantidade = parseFloat($('#quantidade').val().replace(/\./g,'').replace(',','.')) || 0;
        let valorUnitario = parseFloat($('#valor_unitario').val().replace(/\./g,'').replace(',','.')) || 0;
        let valorTotal = valorUnitario * quantidade;
        $('#valor_total_pedido').text(valorTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        calcularDiferenca();
    }

    function calcularDiferenca() {
        let valorBase = parseFloat($('#valor_recurso_financeiro').text().replace(/\./g,'').replace(',','.')) || 0;
        let totalLimites = parseFloat($('#valor_limites_cadastrados').text().replace(/\./g,'').replace(',','.')) || 0;
        let valorPedido = parseFloat($('#valor_total_pedido').text().replace(/\./g,'').replace(',','.')) || 0;

        let diferenca = valorBase - (totalLimites + valorPedido);

        $('#diferenca_valores').text(diferenca.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#diferenca_valores').removeClass('valor-positivo valor-negativo valor-zero');

        if (diferenca > 0) $('#diferenca_valores').addClass('valor-positivo');
        else if (diferenca < 0) $('#diferenca_valores').addClass('valor-negativo');
        else $('#diferenca_valores').addClass('valor-zero');
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
    $('form').on('submit', function(e) {
        e.preventDefault(); //garantir que o evento é cancelado caso necessário

        let quantidade = $('#quantidade').val();
        if (quantidade) $('#quantidade').val(quantidade.replace(/\./g,''));

        let valorUnitario = $('#valor_unitario').val();
        if (valorUnitario) $('#valor_unitario').val(valorUnitario.replace(/\./g,'').replace(',','.'));

        let diferenca = parseFloat($('#diferenca_valores').text().replace(/\./g,'').replace(',','.')) || 0;
        if (diferenca < 0) {
            mostrarMensagem(
                'Atenção! O valor total do pedido + limites já cadastrados excede o valor do recurso dessa revista.',
                'danger'
            );
            return false;
        }

        this.submit();
    });



});

function mostrarMensagem(mensagem, tipo = 'success') {
    const alertHtml = `
        <div class="alert alert-${tipo} alert-dismissible fade show mt-3" role="alert">
            ${mensagem}
        </div>
    `;
    $('#alert-container').html(alertHtml);

    // Fecha automaticamente após 3 segundos
    setTimeout(() => {
        $('.alert').alert('close');
    }, 3000);
}
</script>


@endsection
