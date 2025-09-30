@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        <span>Recursos financeiros: {{$licitacao->descricao }} - {{ $licitacao->ano }}</span>
    </div>

    <div class="card-body">
        <input type="hidden" id="licitacao_id" value="{{ $licitacao->id }}">

        <div class="mb-3">
            <label for="revistas" class="form-label font-weight-bold">Revistas</label>
            <select name="revistas[]" id="revistas" class="form-control select2" multiple="multiple">
                @foreach($revistas as $revista)
                    <option value="{{ $revista->id }}"
                        @if($saldosExistentes->pluck('revista_id')->contains($revista->id)) selected @endif>
                        {{ $revista->name }} - {{ $revista->centro_despesa }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="card mt-2 mb-3">
            <table class="table table-hover table-striped" id="tabela-saldos">
                <thead>
                    <tr>
                        <th class="text-center">Revista</th>
                        <th class="text-center">Recurso recebido</th>
                        <th class="text-center">Recurso reseervado para estágio</th>
                        <th class="text-center">Ações</th>
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
                            <button class="btn btn-success btn-sm salvar" title="Salvar"><i class="fas fa-check"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="container mt-3 col">
            <a href="{{ route('licitacoes.show', $licitacao->id) }}" class="btn btn-secondary">
                <i class="fas fa-chevron-left"></i> Voltar
            </a>
        </div>

    </div>
</div>


@section('javascripts_bottom')
<script>
$(document).ready(function(){

    // Inicializa Select2
    $('.select2').select2();

    // Formato moeda
    $('.money').mask('0.000.000.000.000,00', { reverse: true });

    // Função para atualizar saldo (recurso recebido - recurso estágio)
    function atualizarSaldo(row){
        var recursoRecebido = parseFloat(row.find('.recurso-recebido').val().replace(/\./g, '').replace(',', '.')) || 0;
        var recursoEstagio = parseFloat(row.find('.recurso-estagio').val().replace(/\./g, '').replace(',', '.')) || 0;
        var saldo = recursoRecebido - recursoEstagio;
        row.find('.saldo').text(saldo.toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2}));
    }

    // Atualiza saldo ao digitar
    $('#tabela-saldos').on('input', '.recurso-recebido, .recurso-estagio', function(){
        var row = $(this).closest('tr');
        atualizarSaldo(row);
    });

    // Adicionar novas linhas quando selecionar revistas no select2
    $('#revistas').on('change', function() {
        var selected = $(this).val(); // array de IDs selecionadas
        selected.forEach(function(revistaId) {
            // só adiciona se ainda não existir na tabela
            if ($('#tabela-saldos tbody tr[data-revista-id="'+revistaId+'"]').length == 0) {
                var revistaName = $('#revistas option[value="'+revistaId+'"]').text();
                $('#tabela-saldos tbody').append(`
                    <tr data-revista-id="${revistaId}">
                        <td>${revistaName}</td>
                        <td><input type="text" class="form-control money recurso-recebido" placeholder="0,00"></td>
                        <td><input type="text" class="form-control money recurso-estagio" placeholder="0,00"></td>
                        <td>
                            <button class="btn btn-success btn-sm salvar"><i class="fas fa-check"></i></button>
                        </td>
                    </tr>
                `);

                // reaplica a máscara moeda nos novos inputs
                $('.money').mask('0.000.000.000.000,00', { reverse: true });
            }
        });
    });

    // Salvar saldo via botão
    $('#tabela-saldos').on('click', '.salvar', function(e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        var revistaId = row.data('revista-id');
        var recursoRecebido = parseFloat(row.find('.recurso-recebido').val().replace(/\./g, '').replace(',', '.')) || 0;
        var recursoEstagio = parseFloat(row.find('.recurso-estagio').val().replace(/\./g, '').replace(',', '.')) || 0;
        var licitacaoId = $('#licitacao_id').val();

        $.ajax({
            url: "{{ route('saldos.store') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                licitacao_id: licitacaoId,
                revista_id: revistaId,
                recurso_recebido: recursoRecebido,
                recurso_estagio: recursoEstagio
            },
            success: function(response) {
                if(response.success){
                    alert('Saldo salvo com sucesso!');
                } else {
                    alert('Erro ao salvar saldo.');
                }
            },
            error: function(){
                alert('Erro ao salvar saldo.');
            }
        });
    });

});
</script>
@endsection



@endsection
