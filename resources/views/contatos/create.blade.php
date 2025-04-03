@extends('layouts.app')

@section('content')

<div class="card">
    <div class="card-header h5">
        Cadastrar Contato para a empresa {{ $empresa->razao_social }}
    </div>
    <div class="card-body">
        @can('admin')
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

<script>
$(document).ready(function() {
    // Formato cnpj
    $('.telefone9digitos').mask("(00) 90000-0000");
});
</script>

<script type="text/javascript">
var path = "{{ route('autocomplete') }}";

$('#search').typeahead({
    source: function(query, process) {
        return $.get("{{ route('autocomplete') }}", {
            query: query
        }, function(data) {
            return process(data.map(function(item) {
                return {
                    name: item.name,
                    email: item.email,
                    telefone: item.telefone,
                    display: item.name + ' - ' + item
                        .email // Apenas o que será mostrado
                };
            }));
        });
    },
    displayText: function(item) {
        return item.display; // Mostra apenas "nome - email"
    },
    updater: function(item) {
        $('#search').val(item.name); // Apenas o nome no campo de busca
        $('#email').val(item.email).prop('readonly', true); // Preenche e-mail
        $('#telefone').val(item.telefone).prop('readonly', true); // Preenche telefone
        $('#cadastrar').prop('disabled', true); // Desabilita o botão "Salvar"

        $('#mensagem-usuario').fadeIn(); // Exibe a mensagem e só some se o usuário fechar

        return item.name; // Retorna apenas o nome no campo de busca
    }
});
</script>
<script>
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