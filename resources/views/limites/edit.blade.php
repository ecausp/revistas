<!-- resources/views/limites/edit.blade.php -->
@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Editar Licitação
    </div>
    <div class="card-body">

        @can('revistas.financeiro')
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
<link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
@endsection


@section('javascripts_bottom')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
@parent]<script>
$(document).ready(function() {
    console.log('O script foi carregado!');
});

$(document).ready(function() {
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

                    $('#valor_unitario').val(valorFormatado);
                },
                error: function() {
                    alert('Erro ao buscar valor unitário do serviço.');
                }
            });
        }
    }

    // Inicializa Select2
    $('.select2').select2();

    // Evento correto para capturar seleção com Select2
    $('#servico_id').on('select2:select', function(e) {
        const servicoId = $(this).val();
        carregarValorUnitario(servicoId);
    });

    // Carrega valor unitário automaticamente no modo edit
    const servicoSelecionado = $('#servico_id').val();
    if (servicoSelecionado) {
        carregarValorUnitario(servicoSelecionado);
    }
});
</script>


<script>
$(document).ready(function() {
    // Máscara para campo monetário
    $('.money').mask('000.000.000.000.000,00', {
        reverse: true
    });

    // Ao enviar o form, libera o campo readonly e converte o valor para o formato do Laravel
    $('form').on('submit', function() {
        $('#valor_unitario').prop('readonly', false); // libera pra submissão

        let valorComMascara = $('#valor_unitario').val();
        let valorLimpo = valorComMascara.replace(/\./g, '').replace(',', '.');
        $('#valor_unitario').val(valorLimpo);
    });
});
</script>



@endsection