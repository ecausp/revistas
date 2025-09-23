@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Cadastrar Contato para a empresa {{ $empresa->razao_social }}
    </div>
    <div class="card-body">
        @can('admin-financeiro')
        <form action="{{ route('contatos.store') }}" method="POST">
            @csrf
            @include('contatos.partials.forms')
        </form>
        @endcan
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection


@section('javascripts_bottom')
<!-- JQUERY -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- Select2 para selects com busca -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Typeahead (autocomplete para inputs de texto) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
<!-- jQuery Mask (para formatar inputs como telefone, CPF, etc.) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<!-- jQuery UI (datepicker, etc.) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>


<script>
//mask do telefone
$(document).ready(function() {
    $('.telefone9digitos').mask("(00) 90000-0000");
});
</script>


<script type="text/javascript">
// script para o autocomplete pra pegar o user
var path = "{{ route('autocomplete') }}";

$('#search').typeahead({
    source: function(query, process) {
        return $.get(path, {
            query: query
        }, function(data) {
            // Mapeia os dados retornados para o formato necessário
            return process(data.map(function(item) {
                return {
                    id: item.user_id,
                    name: item.name,
                    email: item.email,
                    telefone: item.telefone,
                    empresa_id: item.empresa_id,
                    razao_social: item.razao_social,
                    cnpj: item.cnpj,
                    display: item.name + ' - ' + item.email
                };
            }));
        });
    },
    displayText: function(item) {
        // O texto exibido na lista de sugestões
        return item.display;
    },
    updater: function(item) {
        $('#search').val(item.name);
        $('#email').val(item.email).prop('readonly', true);
        $('#telefone').val(item.telefone).prop('readonly', true);

        if(item.empresa_id) {
            // usuário associado a empresa
            $('#cadastrar').prop('disabled', true);
            $('#empresa_id').val(item.empresa_id).prop('readonly', true);

            $('#mensagem-texto').html(`
                Este usuário já está cadastrado na empresa: <br>
                <strong>${item.razao_social}</strong> <br>
                CNPJ: <strong>${item.cnpj}</strong>
            `);
            $('#mensagem-usuario').fadeIn();
        } else {
            // usuário NÃO está associado a empresa
            $('#cadastrar').prop('disabled', false);
            $('#empresa_id').val("{{ $empresa->id }}").prop('readonly', true); // garante empresa atual
            $('#mensagem-usuario').fadeOut();
            $('#telefone').prop('readonly', false);
        }

        return item.name;
    }
});

// Fechar a mensagem ao clicar no botão "X"
document.getElementById('fechar-mensagem').addEventListener('click', function() {
    document.getElementById('mensagem-usuario').style.display = 'none';
});
</script>


<script type="text/javascript">
$(document).ready(function() {
    // Limpar os campos de e-mail e telefone e torná-los editáveis no carregamento da página
    $('#name').val('').prop('readonly', false);
    $('#email').val('').prop('readonly', false);
    $('#telefone').val('').prop('readonly', false);
    $('#cadastrar').prop('disabled', false);
});
</script>

@endsection