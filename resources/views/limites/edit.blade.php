<!-- resources/views/limites/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div id="alert-container"></div>

<div class="card">
    <div class="card-header h5">
        Editar Limite
    </div>
    <div class="card-body">

        @can("admin-financeiro")
        <form action="{{ route('limites.update', $limite) }}" method="POST">
            @csrf
            @method('PUT')
            @include('limites.partials.forms')
        </form>
    </div>
    @endcan
</div>
@endsection

{{-- 
Não encontrei o viewMode years no JQuery Datepicker do Laravel USP Theme.
Precisei utilizar o Bootstrap Datepicker.
TODO: Verificar se o Bootstrap Datepicker funciona com o Laravel USP Theme.  
--}}
@section('styles')
@parent
<style>
.valor-negativo {
    color: #dc3545 !important;
}

.valor-positivo {
    color: #28a745 !important;
}

.valor-zero {
    color: #6c757d !important;
}

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
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection

@section('javascripts_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<!-- Select2 principal -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Tradução para português do Brasil -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>

@parent
<script>
$(document).ready(function() {
    const urlDadosRelacao = "{{ route('licitacoes-servicos.dados') }}";

    // O valor unitário pertence à relação entre licitação, serviço e empresa vigente.
    function carregarValorUnitario() {
        let licitacaoId = $('#licitacao_id').val();
        let servicoId = $('#servico_id').val();

        if (!licitacaoId || !servicoId) return;

        $.get(urlDadosRelacao, {
            licitacao_id: licitacaoId,
            servico_id: servicoId
        }).done(function(data) {
            let valor = Number.parseFloat(data.valor_unitario);

            if (!Number.isFinite(valor)) {
                $('#valor_unitario').val('');
                atualizarValorTotal();
                return;
            }

            $('#valor_unitario').val(valor.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            atualizarValorTotal();
        }).fail(function() {
            $('#valor_unitario').val('');
            atualizarValorTotal();
            alert('Não foi encontrado valor unitário para a licitação e o serviço selecionados.');
        });
    }

    // Função para carregar o valor da licitação
    function carregarValorLicitacao(licitacaoId) {
        if (licitacaoId) {
            $.ajax({
                url: `/licitacoes/${licitacaoId}/valor-total`,
                type: 'GET',
                success: function(data) {
                    let valorFormatado = parseFloat(data.valor_total).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    $('#valor_total_licitacao').text(valorFormatado);
                    calcularSaldo();
                },
                error: function() {
                    alert('Erro ao buscar valor total da licitação.');
                }
            });
        }
    }

    // Função para calcular e atualizar o valor total do pedido
    function atualizarValorTotal() {
        let quantidade = parseFloat($('#quantidade').val().replace(/\./g, '').replace(',', '.') || 0);
        let valorUnitario = parseFloat($('#valor_unitario').val().replace(/\./g, '').replace(',', '.') || 0);

        let valorTotal = valorUnitario * quantidade;
        let valorTotalFormatado = valorTotal.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        $('#valor_total_pedido').text(valorTotalFormatado);
        calcularSaldo();
    }

    // Função para calcular o saldo entre os valores
    function calcularSaldo() {
        // Pega os valores formatados
        let valorLicitacao = $('#valor_total_licitacao').text();
        let valorPedido = $('#valor_total_pedido').text().replace('-', '').trim();

        // Converte para números
        let valorLicitacaoNum = valorLicitacao ?
            parseFloat(valorLicitacao.replace(/\./g, '').replace(',', '.')) : 0;
        let valorPedidoNum = valorPedido ?
            parseFloat(valorPedido.replace(/\./g, '').replace(',', '.')) : 0;

        let saldo = valorLicitacaoNum - valorPedidoNum;

        // Formata o resultado
        let saldoFormatado = saldo.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        let saldoElement = $('#saldo_valores');
        saldoElement.text(saldoFormatado);

        // Estiliza conforme o valor
        saldoElement.removeClass('valor-positivo valor-negativo valor-zero');
        if (saldo > 0) {
            saldoElement.addClass('valor-positivo');
        } else if (saldo < 0) {
            saldoElement.addClass('valor-negativo');
        } else {
            saldoElement.addClass('valor-zero');
        }
    }

    // Inicializa o Select2
    $('.select2').select2({language: "pt-BR"});

    // Eventos
    $('#licitacao_id, #servico_id').on('select2:select', function() {
        carregarValorUnitario();
    });

    $('#licitacao_id').on('select2:select', function(e) {
        carregarValorLicitacao($(this).val());
    });

    // Carrega valores iniciais se existirem
    if ($('#licitacao_id').val() && $('#servico_id').val()) {
        carregarValorUnitario();
    }
    if ($('#licitacao_id').val()) {
        carregarValorLicitacao($('#licitacao_id').val());
    }

    // Máscara para campos monetários
    $('.money').mask('000.000.000.000.000,00', {
        reverse: true
    });

    $('#quantidade').mask('000.000.000.000', {
        reverse: true
    });

    // Atualiza cálculos quando valores mudam
    $('#quantidade, #valor_unitario').on('input', function() {
        atualizarValorTotal();
    });

    // Formata valores antes do submit
    $('form').on('submit', function() {
        let quantidade = $('#quantidade').val();
        if (quantidade) $('#quantidade').val(quantidade.replace(/\./g,''));

        let valorUnitario = $('#valor_unitario').val();
        if (valorUnitario) $('#valor_unitario').val(valorUnitario.replace(/\./g,'').replace(',','.'));
    });

    
});
</script>
@endsection
