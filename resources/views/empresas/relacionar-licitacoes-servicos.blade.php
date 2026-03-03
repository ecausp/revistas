@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Relacionar Licitações e Serviços para a Empresa: {{ $empresa->razao_social }}
    </div>
    <div class="card-body">
        <form action="{{ route('empresas.salvar-relacionamento', $empresa) }}" method="POST">
            @csrf

            {{-- 1) Escolher a licitação --}}
            <div class="mb-4">
                <div class="card">
                    <div class="card-header h6">1) Selecione a Licitação:</div>
                    <div class="card-body">
                        <select name="licitacao_id" id="licitacao_id" class="form-select" required>
                            <option value="">-- Selecione --</option>
                            @foreach($licitacoes_associadas as $licitacao)
                                <option value="{{ $licitacao->id }}">{{ $licitacao->descricao }} ({{ $licitacao->ano }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- 2) Selecionar serviços --}}
            <div class="mb-4">
                <div class="card">
                    <div class="card-header h6">2) Selecione os Serviços:</div>
                    <div class="card-body">
                        <select name="servicos[]" id="servicos" class="form-select" multiple>
                        @foreach($todos_servicos as $servico)
                            @php
                                // verifica se este serviço já está associado à licitação e empresa
                                $selected = false;
                                foreach($servicos_para_view as $servicos_por_licitacao){
                                    foreach($servicos_por_licitacao as $s){
                                        if($s['id'] == $servico->id){
                                            $selected = true;
                                            break 2;
                                        }
                                    }
                                }
                            @endphp
                            <option value="{{ $servico->id }}" {{ $selected ? 'selected' : '' }}>
                                {{ $servico->descricao }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            </div>

            {{-- Tabela de serviços selecionados --}}
            <div class="card">
                <div class="card-header h6">3) Preencha os Detalhes:</div>
                    <div class="card-body">
            <div id="tabela-servicos-container" class="mb-4">
                <x-table-simples>
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Valor Unitário</th>
                            <th>Prazo (dias)</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-servicos-body">
                        {{-- Linhas serão preenchidas via JS --}}
                    </tbody>
                </x-table-simples>
            </div>

            <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
    </div>
</div>
@endsection




@section('styles')
@parent
<style>
.select2-container {
    width: 100% !important;
}
</style>
@endsection

@section('javascripts_bottom')
@parent
<script>
$(document).ready(function() {
    // Inicializa Select2
    $('#licitacao_id').select2({ placeholder: '-- Selecione --' });
    $('#servicos').select2({ placeholder: 'Selecione os serviços' });

    // Dados pré-carregados para preencher a tabela
    let servicosAssociados = @json($servicos_para_view);

    function atualizarTabela() {
        let licitacaoSelecionada = $('#licitacao_id').val();
        let servicosSelecionados = $('#servicos').val() || [];
        let tbody = $('#tabela-servicos-body');
        tbody.empty();

        servicosSelecionados.forEach(function(id, index) {
            let descricao = $('#servicos option[value="'+id+'"]').text();
            let valor_unitario = '';
            let prazo = '';

            if(licitacaoSelecionada && servicosAssociados[licitacaoSelecionada]){
                let s = servicosAssociados[licitacaoSelecionada].find(x => x.id == id);
                if(s){
                    valor_unitario = s.valor_unitario;
                    prazo = s.prazo;
                }
            }

            tbody.append(
                `<tr>
                    <td>${descricao}</td>
                    <td>
                        <input type="hidden" name="servicos[${index}][id]" value="${id}">
                        <input type="text" name="servicos[${index}][valor_unitario]" class="form-control valor_unitario" value="${valor_unitario}">
                    </td>
                    <td>
                        <input type="number" name="servicos[${index}][prazo]" class="form-control" value="${prazo}">
                    </td>
                </tr>`
            );
        });

        // Aplica máscara de moeda
        $('.valor_unitario').mask('#.##0,00', {reverse: true});
    }

    // Atualiza tabela ao mudar seleção
    $('#servicos, #licitacao_id').on('change', atualizarTabela);

    // Atualiza tabela ao carregar a página
    atualizarTabela();
});
</script>

@endsection
