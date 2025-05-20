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
        </form>
    </div>
</div>
@endsection

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
@endsection


@section('javascripts_bottom')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('search');
    const infoDiv = document.getElementById('info-usuario');

    input.addEventListener('input', function() {
        const nome = this.value.trim();

        if (nome.length >= 3) {
            fetch(`/buscar-usuario?nome=${encodeURIComponent(nome)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.email && data.codpes) {
                        infoDiv.innerHTML = `
                                <br>
                                <p><strong>Confirme as informações do Solicitante para adicioná-lo:</strong><br>
                                <strong>Email:</strong> ${data.email}<br>
                                <strong>Codpes:</strong> ${data.codpes}</p>
                            `;
                    } else {
                        infoDiv.innerHTML = `<em>Usuário não encontrado</em>`;
                    }
                })
                .catch(error => {
                    console.error('Erro na busca:', error);
                    infoDiv.innerHTML = `<em>Erro ao buscar usuário</em>`;
                });
        } else {
            infoDiv.innerHTML = '';
        }
    });
});
</script>



@endsection