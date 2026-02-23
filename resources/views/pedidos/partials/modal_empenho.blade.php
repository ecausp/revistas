@if ($pedido->servicos->count() > 0)

{{-- CORREÇÃO 1: data-backdrop (sem bs) para Bootstrap 4/5 Legacy --}}
<div class="modal fade" id="modalAprovar" tabindex="-1" aria-labelledby="modalAprovarLabel" aria-hidden="true"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="card mt-4">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 me-2 mr-5">Enviar Pedido para Execução</h5>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="formAprovar" action="{{ route('pedidos.execucao', $pedido) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="num_notificacao_fornecimento" class="form-label">Número da Notificação de Fornecimento</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="num_notificacao_fornecimento" 
                                   name="num_notificacao_fornecimento"
                                   value="{{ old('num_notificacao_fornecimento', $pedido->num_notificacao_fornecimento) }}" 
                                   required>
                        </div>

                        <p>Preencha o número do empenho para cada serviço antes de enviar:</p>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Empresa</th>
                                        <th style="width: 25%;">Descrição</th>
                                        <th style="width: 10%;">Unidade de Medida</th>
                                        <th style="width: 10%;">Valor Unitário (R$)</th>
                                        <th style="width: 10%;">Quantidade</th>
                                        <th style="width: 10%;">Valor Total (R$)</th>
                                        <th style="width: 20%;">Número de Empenho</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $valorTotalPedido = 0;
                                    @endphp

                                    @foreach ($pedido->servicos as $servico)
                                        @php
                                            // 1. Busca Empresa no Mapa (Enviado pelo Controller)
                                            $dadosRelacao = $dados->get($servico->id);
                                            $nomeEmpresa = $dadosRelacao->razao_social ?? 'Empresa não identificada';

                                            // 2. Dados Reais do Pedido (Pivot)
                                            $quantidade = $servico->pivot->quantidade;
                                            $valorUnitario = $servico->pivot->valor_unitario; // Pega o valor gravado, não o genérico

                                            // 3. Totais
                                            $valorTotalServico = $quantidade * $valorUnitario;
                                            $valorTotalPedido += $valorTotalServico;
                                        @endphp

                                        <tr id="servico-{{ $servico->id }}">
                                            {{-- Empresa Corrigida --}}
                                            <td>{{ $nomeEmpresa }}</td>
                                            
                                            <td>{{ $servico->descricao }}</td>
                                            <td>{{ $servico->unidade_medida }}</td>
                                            
                                            {{-- Valor Unitário Corrigido (Pivot) --}}
                                            <td class="text-right">{{ number_format($valorUnitario, 2, ',', '.') }}</td>
                                            
                                            <td class="text-right">{{ number_format($quantidade, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($valorTotalServico, 2, ',', '.') }}</td>
                                            
                                            <td>
                                                <input type="text" 
                                                    class="form-control" 
                                                    name="empenhos[{{ $servico->pivot->id }}]" 
                                                    value="{{ old('empenhos.' . $servico->pivot->id) }}" 
                                                    placeholder="Nº Empenho"
                                                    required>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td class="text-right"><strong>Valor total do pedido: </strong></td>
                                        <td class="text-right"><strong>{{ number_format($valorTotalPedido, 2, ',', '.') }}</strong></td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" form="formAprovar">
                        <i class="fas fa-tools"></i> Enviar para Execução
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif