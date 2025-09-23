@extends('layouts.app')

@section('content')


<div class="card">
    <div class="card-header h5">
        Cadastrar Solicitante para a Revista {{ $revista->name }}
    </div>

    <div class="card-body">
        <form action="{{ route('solicitantes.store') }}" method="POST">
            @csrf
            @include('solicitantes.partials.forms')

            <input type="hidden" id="revista_id" name="revista_id" value="{{ $revista->id }}">
        </form>
    </div>
</div>

<div id="mensagem-usuario" class="alert alert-warning" style="display:none;">
    <span id="mensagem-texto"></span>
    <button type="button" id="fechar-mensagem" class="btn btn-sm btn-secondary">Fechar</button>
</div>

@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('javascripts_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const route = '/buscar-usuario';

    $('#search').typeahead({
        minLength: 3,
        autoSelect: true,
        source: function(query, process) {
            return $.get(route, { 
                nome: query, 
                revista_id: $('#revista_id').val() 
            }, function(data) {
                if (Array.isArray(data)) {
                    return process(data.map(item => ({
                        ...item,
                        display: `${item.name} (${item.email})`
                    })));
                } else {
                    return process([]);
                }
            });
        },
        displayText: function(item) {
            return item.display;
        },
        afterSelect: function(item) {
            $('#search').val(item.name);
            $('#email').val(item.email);
            $('#codpes').val(item.codpes);
            $('#user_id').val(item.id);

            if (item.ja_cadastrado) {
                $('#mensagem-texto').html(
                    `Este solicitante já está cadastrado na revista <strong>{{ $revista->name }}</strong>.`
                );
                $('#mensagem-usuario').fadeIn();
                $('#cadastrar').prop('disabled', true);
            } else {
                $('#mensagem-usuario').hide();
                $('#cadastrar').prop('disabled', false);
            }
        }
    });

    // Botão fechar da mensagem
    $('#fechar-mensagem').on('click', function() {
        $('#mensagem-usuario').hide();
        $('#cadastrar').prop('disabled', false);
    });
});
</script>
@endsection
