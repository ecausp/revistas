<!-- resources/views/limites/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Licitação
    </div>
    <div class="card-body">

        @can('admin-revista')
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
@parent
<script>
$(document).ready(function() {
    // Função para carregar o valor unitário via AJAX
    function carregarValorUnitario(servicoId) {
        if (servicoId) {
            $.ajax({
                url: `/servicos/${servicoId}/valor-unitario`,
                type: 'GET',
                success: function(data) {
                    let valorFormatado = parseFloat(data.valor_unitario).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    $('#valor_unitario').val(valorFormatado).prop('readonly', true);
                    atualizarValorTotal();
                },
                error: function() {
                    alert('Erro ao buscar valor unitário do serviço.');
                }
            });
        }
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
                    calcularDiferenca();
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
        calcularDiferenca();
    }

    // Função para calcular a diferença entre os valores
    function calcularDiferenca() {
        // Pega os valores formatados
        let valorLicitacao = $('#valor_total_licitacao').text();
        let valorPedido = $('#valor_total_pedido').text().replace('-', '').trim();

        // Converte para números
        let valorLicitacaoNum = valorLicitacao ?
            parseFloat(valorLicitacao.replace(/\./g, '').replace(',', '.')) : 0;
        let valorPedidoNum = valorPedido ?
            parseFloat(valorPedido.replace(/\./g, '').replace(',', '.')) : 0;

        // Calcula a diferença
        let diferenca = valorLicitacaoNum - valorPedidoNum;

        // Formata o resultado
        let diferencaFormatada = diferenca.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        // Atualiza o campo da diferença
        let diferencaElement = $('#diferenca_valores');
        diferencaElement.text(diferencaFormatada);

        // Estiliza conforme o valor
        diferencaElement.removeClass('valor-positivo valor-negativo valor-zero');
        if (diferenca > 0) {
            diferencaElement.addClass('valor-positivo');
        } else if (diferenca < 0) {
            diferencaElement.addClass('valor-negativo');
        } else {
            diferencaElement.addClass('valor-zero');
        }
    }

    // Inicializa o Select2
    $('.select2').select2();

    // Eventos
    $('#servico_id').on('select2:select', function(e) {
        carregarValorUnitario($(this).val());
    });

    $('#licitacao_id').on('select2:select', function(e) {
        carregarValorLicitacao($(this).val());
    });

    // Carrega valores iniciais se existirem
    if ($('#servico_id').val()) {
        carregarValorUnitario($('#servico_id').val());
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
        if (quantidade) {
            $('#quantidade').val(quantidade.replace(/\./g, ''));
        }
        // Converte valor_unitario
        let valorUnitario = $('#valor_unitario').val();
        if (valorUnitario) {
            $('#valor_unitario').val(valorUnitario.replace(/\./g, '').replace(',', '.'));
        }

        // Validação adicional
        let diferencaText = $('#diferenca_valores').text();
        let diferenca = parseFloat(diferencaText.replace(/\./g, '').replace(',', '.'));
        if (diferenca < 0) {
            alert('Atenção! O valor total do pedido excede o valor da licitação.');
            return false;
        }
    });
});
</script>
@endsection