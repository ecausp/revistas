<!-- resources/views/limites/create.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Criar limite
    </div>
    <div class="card-body">
        @can('revistas.financeiro')
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
                    $('#valor_total_licitacao').val(valorFormatado);
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

        $('#valor_total_pedido').val(valorTotalFormatado);
        calcularDiferenca();
    }

    // Função para calcular a diferença entre os valores
    function calcularDiferenca() {
        // Pega os valores formatados
        let valorLicitacao = $('#valor_total_licitacao').val();
        let valorPedido = $('#valor_total_pedido').val();

        // Converte para números (remove pontos, troca vírgula por ponto)
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

        // Atualiza o campo
        $('#diferenca_valores').val(diferencaFormatada);

        // Muda a cor se a diferença for negativa
        if (diferenca < 0) {
            $('#diferenca_valores').addClass('text-danger');
        } else {
            $('#diferenca_valores').removeClass('text-danger');
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

    // Atualiza cálculos quando valores mudam
    $('#quantidade, #valor_unitario').on('input', function() {
        atualizarValorTotal();
    });

    // Formata valores antes do submit
    $('form').on('submit', function() {
        // Converte valor_unitario
        let valorUnitario = $('#valor_unitario').val();
        if (valorUnitario) {
            $('#valor_unitario').val(valorUnitario.replace(/\./g, '').replace(',', '.'));
        }

        // Converte valor_total_pedido
        let valorTotalPedido = $('#valor_total_pedido').val();
        if (valorTotalPedido) {
            $('#valor_total_pedido').val(valorTotalPedido.replace(/\./g, '').replace(',', '.'));
        }

        // Validação adicional (opcional)
        let diferenca = parseFloat($('#diferenca_valores').val().replace(/\./g, '').replace(',', '.'));
        if (diferenca < 0) {
            alert('Atenção! O valor total do pedido excede o valor da licitação.');
            return false;
        }
    });
});
</script>



@endsection