@if ($licitacao->empresas->count() > 0)
<div class="card mt-2 mb-3">
    {{-- 1. Título movido para o Card Header para não quebrar o DataTables --}}
    <div class="card-header font-weight-bold">
        Empresas Participantes
    </div>

    <div class="card-body p-0">

        <x-table-simples>
            <thead>
                <tr>
                    <th>Razão Social</th>
                    <th>CNPJ</th>
                    <th class="text-center">Serviços</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($licitacao->empresas as $empresa)
                <tr>
                    <td>{{ $empresa->razao_social }}</td>
                    <td>{{ $empresa->cnpj }}</td>
                    <td class="text-center">
                        @if ($empresa->servicos->count() > 0)
                            {{-- 
                                BOTÃO DO MODAL
                            --}}
                            <button class="btn btn-sm btn-primary text-white" type="button" 
                                    data-toggle="modal" data-target="#modal-servicos-{{ $empresa->id }}">
                                <i class="fas fa-list"></i> Ver Serviços
                            </button>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-primary btn-sm" title="Ver Cadastro">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table-simples>
    </div>
</div>

{{-- 
    3. LOOP DOS MODAIS (FORA DA TABELA)
    Colocamos isso aqui fora para que o DataTables não delete o HTML dos modais
    quando você mudar de página na paginação da tabela.
--}}
@foreach ($licitacao->empresas as $empresa)
    @if ($empresa->servicos->count() > 0)
    <div class="modal fade" id="modal-servicos-{{ $empresa->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Serviços: <strong>{{ $empresa->razao_social }}</strong>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    {{-- 
                        Tabela Interna (Dentro do Modal)
                        Aqui usamos uma tabela HTML padrão do Bootstrap (sem DataTables)
                        para evitar conflito de "tabela dentro de modal oculto".
                    --}}
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Serviço</th>
                                    <th>Unidade</th>
                                    <th class="text-right">Prazo (dias)</th>
                                    <th class="text-right">Valor Unit. (R$)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($empresa->servicos as $servico)
                                <tr>
                                    <td>{{ $servico->descricao }}</td>
                                    <td>{{ $servico->unidade_medida }}</td>
                                    <td class="text-right">{{ $servico->prazo }}</td>
                                    <td class="text-right">{{ number_format($servico->valor_unitario, 2, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endif