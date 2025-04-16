@section('javascripts_bottom')
@parent
<script>
$(document).ready(function() {
    // Formato número
    $('.numero').mask('000.000.000.000.000', {
        reverse: true
    });
    // Formato moeda
    $('.money').mask('000.000.000.000.000,00', {
        reverse: true
    });
});
</script>

<script>
$('#novoServicoForm').on('submit', function(e) {
    e.preventDefault(); // Evita que o formulário seja enviado de forma tradicional

    var form = $(this);
    var actionUrl = form.attr('action');

    $.ajax({
        url: actionUrl,
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            // Adicionar o novo serviço ao select2
            var newOption = new Option(response.descricao, response.id, false, false);
            $('#servico_id').append(newOption).trigger('change');

            // Fechar o modal
            $('#servicoModal').modal('hide');

            // Opcional: Limpar o formulário
            form[0].reset();
        },
        error: function(xhr) {
            // Lidar com erros, se houver
            alert('Erro ao cadastrar o serviço');
        }
    });
});
</script>

@endsection