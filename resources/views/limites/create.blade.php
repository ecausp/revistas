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

                    // Definir o valor no campo e mantê-lo readonly
                    $('#valor_unitario').val(valorFormatado).prop('readonly', true);

                    // Atualiza o valor total após carregar o valor unitário
                    atualizarValorTotal();
                },
                error: function() {
                    alert('Erro ao buscar valor unitário do serviço.');
                }
            });
        }
    }

    // Função para calcular e atualizar o valor total
    function atualizarValorTotal() {
        let quantidade = parseFloat($('#quantidade').val().replace('.', '').replace(',', '.'));
        let valorUnitario = parseFloat($('#valor_unitario').val().replace('.', '').replace(',', '.'));

        // Verifica se o valor_unitario está preenchido antes de calcular o valor_total
        if (!isNaN(valorUnitario) && !isNaN(quantidade)) {
            let valorTotal = valorUnitario * quantidade;

            // Formata o valor total para o padrão pt-BR (2 casas decimais)
            let valorTotalFormatado = valorTotal.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            // Atualiza o campo "valor_total" com o valor formatado
            $('#valor_total').val(valorTotalFormatado);
        } else {
            // Se algum dos campos não estiver preenchido, limpa o campo "valor_total"
            $('#valor_total').val('');
        }
    }

    // Inicializa o Select2
    $('.select2').select2();

    // Evento correto para capturar seleção com Select2
    $('#servico_id').on('select2:select', function(e) {
        const servicoId = $(this).val();
        carregarValorUnitario(servicoId); // Atualiza o valor unitário
    });

    // Carrega o valor unitário automaticamente no modo edit
    const servicoSelecionado = $('#servico_id').val();
    if (servicoSelecionado) {
        carregarValorUnitario(servicoSelecionado); // Atualiza o valor unitário
    }

    // Máscara para campos monetários
    $('.money').mask('000.000.000.000.000,00', {
        reverse: true
    });

    // Atualiza o valor total quando a quantidade ou valor unitário for alterado
    $('#quantidade, #valor_unitario').on('input', function() {
        atualizarValorTotal();
    });

    // Ao enviar o form, limpa e converte o valor para o formato do Laravel
    $('form').on('submit', function() {
        // Para o campo valor_unitario, limpa a máscara e converte para número
        let valorComMascara = $('#valor_unitario').val();
        let valorLimpo = valorComMascara.replace(/\./g, '').replace(',', '.');
        $('#valor_unitario').val(valorLimpo);

        // Para o campo valor_total, limpa a máscara e converte para número
        let valorTotalComMascara = $('#valor_total').val();
        let valorTotalLimpo = valorTotalComMascara.replace(/\./g, '').replace(',', '.');
        $('#valor_total').val(valorTotalLimpo);
    });
});
</script>



@endsection