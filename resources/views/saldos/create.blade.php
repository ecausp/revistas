@extends('layouts.app')

@section('content')

<div class="card">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card-header h5">
        <span>Recursos financeiros: {{$licitacao->descricao }} - {{ $licitacao->ano }}</span>
    </div>

    <div class="card-body">
        <input type="hidden" id="licitacao_id" value="{{ $licitacao->id }}">

        {{-- Select2 --}}
        <div class="mb-3">
            <label for="revistas" class="form-label font-weight-bold">Revistas</label>
            <select name="revistas[]" id="revistas" class="form-control select2" multiple="multiple">
                @foreach($revistas as $revista)
                    <option value="{{ $revista->id }}"
                        @if($saldosExistentes->pluck('revista_id')->contains($revista->id)) selected @endif>
                        {{ $revista->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tabela de saldos --}}
        <div class="card mt-2 mb-3 table-responsive">
            <table id="tabela-saldos">
                <thead>
                    <tr>
                        <th class="text-center">Revista</th>
                        <th class="text-center">Recurso recebido</th>
                        <th class="text-center">Recurso reservado para estágio</th>
                        <th class="text-center">Observação</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($saldosExistentes as $saldo)
                    <tr data-revista-id="{{ $saldo->revista_id }}">
                        <td>{{ $saldo->revista->name }}</td>
                        <td colspan="4">
                            {{-- Form de cada linha --}}
                            <form action="{{ route('saldos.store') }}" method="POST" class="d-flex gap-2" onsubmit="return desformatarMoeda(this)">
                                @csrf
                                <input type="hidden" name="licitacao_id" value="{{ $licitacao->id }}">
                                <input type="hidden" name="revista_id" value="{{ $saldo->revista_id }}">

                                <input type="text" name="recurso_recebido" class="form-control money mr-3"
                                    value="{{ number_format($saldo->recurso_recebido, 2, ',', '.') }}"
                                    placeholder="0,00">

                                <input type="text" name="recurso_estagio" class="form-control money mr-3"
                                    value="{{ number_format($saldo->recurso_estagio, 2, ',', '.') }}"
                                    placeholder="0,00">

                                <textarea name="observacao" class="form-control mr-3" rows="1"
                                    placeholder="Outras fontes de recurso">{{ $saldo->observacao }}</textarea>

                                <button type="submit" class="btn btn-success btn-sm ml-2" title="Salvar">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
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

@endsection

@section('javascripts_bottom')
<script>
$(document).ready(function(){

    // Inicializa Select2
    $('.select2').select2();

    // Formato moeda
    $('.money').mask('0.000.000.000.000,00', { reverse: true });

    // Adicionar novas linhas quando selecionar revistas no select2
    $('#revistas').on('change', function() {
        var selected = $(this).val(); // array de IDs selecionadas
        selected.forEach(function(revistaId) {
            if ($('#tabela-saldos tbody tr[data-revista-id="'+revistaId+'"]').length == 0) {
                var revistaName = $('#revistas option[value="'+revistaId+'"]').text();
                $('#tabela-saldos tbody').append(`
                    <tr data-revista-id="${revistaId}">
                        <td>${revistaName}</td>
                        <td colspan="4">
                            <form action="{{ route('saldos.store') }}" method="POST" class="d-flex gap-2" onsubmit="return desformatarMoeda(this)">
                                @csrf
                                <input type="hidden" name="licitacao_id" value="{{ $licitacao->id }}">
                                <input type="hidden" name="revista_id" value="${revistaId}">

                                <input type="text" name="recurso_recebido" class="form-control money mr-3" placeholder="0,00">
                                <input type="text" name="recurso_estagio" class="form-control money mr-3" placeholder="0,00">
                                <textarea name="observacao" class="form-control mr-3" rows="1" placeholder="Outras fontes de recurso"></textarea>

                                <button type="submit" class="btn btn-success btn-sm ml-2">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                `);

                // reaplica a máscara moeda nos novos inputs
                $('.money').mask('0.000.000.000.000,00', { reverse: true });
            }
        });
    });

});

// Função para desformatar moeda antes do submit
function desformatarMoeda(form){
    form.querySelectorAll('.money').forEach(function(input){
        input.value = input.value.replace(/\./g,'').replace(',','.');
    });
    return true; // permite submit
}
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 3000);
    });
</script>

@endsection
