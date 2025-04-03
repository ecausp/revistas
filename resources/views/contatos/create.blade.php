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
        return $.get(path, {
            query: query
        }, function(data) {
            return process(data.map(function(item) {
                return {
                    id: item.id,
                    name: item.name,
                    email: item.email,
                    telefone: item.telefone,
                    empresa_id: item.empresa_id,
                    razao_social: item.razao_social, // Pegando Razão Social
                    cnpj: item.cnpj, // Pegando CNPJ
                    display: item.name + ' - ' + item.email
                };
            }));
        });
    },
    displayText: function(item) {
        return item.display;
    },
    updater: function(item) {
        $('#search').val(item.name);
        $('#email').val(item.email).prop('readonly', true);
        $('#telefone').val(item.telefone).prop('readonly', true);
        $('#cadastrar').prop('disabled', true);

        // Preencher o campo de empresa
        $('#empresa_id').val(item.empresa_id).prop('readonly', true);

        // Atualizar e Exibir a Mensagem com os dados da empresa
        $('#mensagem-texto').html(`
            Este usuário já está cadastrado na empresa: <br>
            <strong>${item.razao_social}</strong> <br>
            CNPJ: <strong>${item.cnpj}</strong>
        `);
        $('#mensagem-usuario').fadeIn();

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