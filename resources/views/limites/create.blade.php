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
    $('#servico_id').on('change', function() {
        let servicoId = $(this).val();

        if (servicoId) {
            $.ajax({
                url: `/servicos/${servicoId}/valor-unitario`,
                type: 'GET',
                success: function(data) {
                    // Formata com vírgula como separador decimal se necessário
                    let valorFormatado = parseFloat(data.valor_unitario).toLocaleString(
                        'pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });

                    $('#valor_unitario').val(valorFormatado);
                },
                error: function() {
                    alert('Erro ao buscar valor unitário do serviço.');
                }
            });
        } else {
            $('#valor_unitario').val('');
        }
    });
});
</script>


@endsection