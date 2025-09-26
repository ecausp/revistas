@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Saldo da Licitação: {{ $licitacao->id }}</h1>
    <p>AQUI VAI SER A CRIAÇÃO DO SALDO</p>
</div>

<div class="mb-3">
    <label for="revistas" class="form-label">Revistas</label>
    <select name="revistas[]" id="revistas" class="form-control select2" multiple="multiple">
        @foreach($revistas as $revista)
            <option value="{{ $revista->id }}"
                @if($saldosExistentes->pluck('revista_id')->contains($revista->id)) selected @endif>
                {{ $revista->name }} - {{ $revista->centro_despesa }}
            </option>
        @endforeach
    </select>
</div>


<table class="table table-bordered" id="tabela-saldos">
    <thead>
        <tr>
            <th>Revista</th>
            <th>Recurso Recebido</th>
            <th>Recurso Estágio</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($saldosExistentes as $saldo)
        <tr data-revista-id="{{ $saldo->revista_id }}">
            <td>{{ $saldo->revista->name }}</td>
            <td>
                <input type="text" class="form-control money recurso-recebido"
                       value="{{ number_format($saldo->recurso_recebido, 2, ',', '.') }}">
            </td>
            <td>
                <input type="text" class="form-control money recurso-estagio"
                       value="{{ number_format($saldo->recurso_estagio, 2, ',', '.') }}">
            </td>
            <td>
                <button class="btn btn-success btn-sm salvar"><i class="fas fa-check"></i></button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


@section('javascripts_bottom')
<script>
$('.select2').on('change', function() {
    var selected = $(this).val(); // array de IDs selecionadas
    selected.forEach(function(revistaId) {
        if($('#tabela-saldos tbody tr[data-revista-id="'+revistaId+'"]').length == 0){
            var revistaName = $('#revistas option[value="'+revistaId+'"]').text();
            var newRow = $(`
                <tr data-revista-id="${revistaId}">
                    <td>${revistaName}</td>
                    <td><input type="text" class="form-control money recurso-recebido" placeholder="0,00"></td>
                    <td><input type="text" class="form-control money recurso-estagio" placeholder="0,00"></td>
                    <td>
                        <button class="btn btn-success btn-sm salvar"><i class="fas fa-check"></i></button>
                    </td>
                </tr>
            `);

            // adiciona a linha na tabela
            $('#tabela-saldos tbody').append(newRow);

            // aplica a máscara moeda aos novos inputs
            newRow.find('.money').mask('0.000.000.000.000,00', { reverse: true });
        }
    });
});
</script>
@endsection


@endsection
