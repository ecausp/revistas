@extends('layouts.app')

@section('content')

<div id="alert-container"></div>

<div class="card">
    <div class="card-header h5">
        Criar limite
    </div>

    <div class="card-body">
        @can('admin-financeiro')

        <form action="{{ route('limites.store') }}" method="POST">
            @csrf

            {{-- =========================
                 SELEÇÕES PRINCIPAIS
            ========================== --}}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="revista_id">Revista</label>
                    <select name="revista_id" id="revista_id" class="form-control select2" required>
                        <option value="">Selecione</option>
                        @foreach ($revistas as $revista)
                            <option value="{{ $revista->id }}">
                                {{ $revista->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="licitacao_id">Licitação</label>
                    <select name="licitacao_id" id="licitacao_id" class="form-control select2" required>
                        <option value="">Selecione</option>
                        @foreach ($licitacoes as $licitacao)
                            <option value="{{ $licitacao->id }}">
                                {{ $licitacao->descricao }} - {{ $licitacao->ano }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label for="servico_id">Serviço</label>
                    <select name="servico_id" id="servico_id" class="form-control select2" required>
                        <option value="">Selecione</option>
                        @foreach ($servicos as $servico)
                            <option value="{{ $servico->id }}">
                                {{ $servico->descricao }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- =========================
                 DADOS AUTOMÁTICOS
            ========================== --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Empresa</label>
                    <input
                        type="text"
                        id="empresa_razao_social"
                        class="form-control"
                        readonly
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label for="valor_unitario">Valor unitário (R$)</label>
                    <input
                        type="text"
                        id="valor_unitario"
                        name="valor_unitario"
                        class="form-control money"
                        readonly
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label for="quantidade">Quantidade</label>
                    <input
                        type="text"
                        id="quantidade"
                        name="quantidade"
                        class="form-control"
                        required
                    >
                </div>
            </div>

            {{-- =========================
                 RESUMO FINANCEIRO
            ========================== --}}
            <div class="valor-disponivel">
                <h6>Resumo financeiro</h6>

                <div class="linha-valor">
                    <span>Recurso disponível</span>
                    <span>R$ <strong id="valor_recurso_financeiro">0,00</strong></span>
                </div>

                <div class="linha-valor">
                    <span>Limites já cadastrados</span>
                    <span>R$ <strong id="valor_limites_cadastrados">0,00</strong></span>
                </div>

                <div class="linha-valor">
                    <span>Valor do pedido</span>
                    <span>R$ <strong id="valor_total_pedido">0,00</strong></span>
                </div>

                <div class="linha-valor total-disponivel">
                    <span>Diferença</span>
                    <span>R$ <strong id="diferenca_valores" class="valor-zero">0,00</strong></span>
                </div>
            </div>

            {{-- =========================
                 AÇÕES
            ========================== --}}
            <div class="mt-4 text-right">
                <button type="submit" class="btn btn-success">
                    Salvar limite
                </button>
            </div>

        </form>
        @endcan
    </div>
</div>

@endsection

@section('styles')
@parent
<style>
.valor-negativo { color: #dc3545 !important; }
.valor-positivo { color: #28a745 !important; }
.valor-zero     { color: #6c757d !important; }

.valor-disponivel {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
}

.linha-valor {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.total-disponivel {
    border-top: 1px dashed #dee2e6;
    padding-top: 10px;
    margin-top: 10px;
    font-weight: bold;
}
</style>
@endsection

@section('javascripts_bottom')
@parent

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function () {

    $('.select2').select2({ language: 'pt-BR' });
    $('.money').mask('000.000.000.000.000,00', { reverse: true });
    $('#quantidade').mask('000.000.000.000', { reverse: true });

    const urlDadosRelacao   = "{{ route('licitacoes-servicos.dados') }}";
    const urlValorRecebido  = "{{ route('saldos.valor-recebido') }}";
    const urlTotalLimites   = "{{ route('limites.total') }}";

    function getRevistaId() {
        return $('#revista_id').val();
    }

    function carregarDadosRelacao() {
        let licitacaoId = $('#licitacao_id').val();
        let servicoId   = $('#servico_id').val();

        if (!licitacaoId || !servicoId) {
            $('#valor_unitario').val('0,00');
            $('#empresa_razao_social').val('');
            atualizarValorTotal();
            return;
        }

        $.get(urlDadosRelacao, {
            licitacao_id: licitacaoId,
            servico_id: servicoId
        }).done(function (data) {
            let valor = parseFloat(data.valor_unitario) || 0;

            $('#valor_unitario').val(
                valor.toLocaleString('pt-BR', { minimumFractionDigits: 2 })
            );

            $('#empresa_razao_social').val(data.empresa.razao_social);
            atualizarValorTotal();
        });
    }

    function carregarValorRecursoFinanceiro() {
        let licitacaoId = $('#licitacao_id').val();
        let revistaId   = getRevistaId();

        if (!licitacaoId || !revistaId) return;

        $.get(urlValorRecebido, {
            licitacao_id: licitacaoId,
            revista_id: revistaId
        }).done(function (data) {
            $('#valor_recurso_financeiro').text(
                parseFloat(data.valor_recurso_financeiro)
                    .toLocaleString('pt-BR', { minimumFractionDigits: 2 })
            );
            carregarTotalLimites();
        });
    }

    function carregarTotalLimites() {
        $.get(urlTotalLimites, {
            licitacao_id: $('#licitacao_id').val(),
            revista_id: getRevistaId()
        }).done(function (data) {
            $('#valor_limites_cadastrados').text(
                parseFloat(data.total_limites)
                    .toLocaleString('pt-BR', { minimumFractionDigits: 2 })
            );
            calcularDiferenca();
        });
    }

    function atualizarValorTotal() {
        let qtd   = parseFloat($('#quantidade').val().replace(/\./g,'').replace(',','.')) || 0;
        let unit  = parseFloat($('#valor_unitario').val().replace(/\./g,'').replace(',','.')) || 0;
        let total = qtd * unit;

        $('#valor_total_pedido').text(
            total.toLocaleString('pt-BR', { minimumFractionDigits: 2 })
        );

        calcularDiferenca();
    }

    function calcularDiferenca() {
        let base   = parseFloat($('#valor_recurso_financeiro').text().replace(/\./g,'').replace(',','.')) || 0;
        let usados = parseFloat($('#valor_limites_cadastrados').text().replace(/\./g,'').replace(',','.')) || 0;
        let pedido = parseFloat($('#valor_total_pedido').text().replace(/\./g,'').replace(',','.')) || 0;

        let diff = base - (usados + pedido);

        $('#diferenca_valores')
            .text(diff.toLocaleString('pt-BR', { minimumFractionDigits: 2 }))
            .removeClass('valor-positivo valor-negativo valor-zero')
            .addClass(diff > 0 ? 'valor-positivo' : diff < 0 ? 'valor-negativo' : 'valor-zero');
    }

    $('#licitacao_id, #servico_id').on('change', carregarDadosRelacao);
    $('#licitacao_id, #revista_id').on('change', carregarValorRecursoFinanceiro);
    $('#quantidade').on('input', atualizarValorTotal);

    $('form').on('submit', function () {
        $('#valor_unitario').val(
            $('#valor_unitario').val().replace(/\./g,'').replace(',','.')
        );
        $('#quantidade').val(
            $('#quantidade').val().replace(/\./g,'')
        );
    });

});
</script>

@endsection
