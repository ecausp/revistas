@if ($licitacao->empresas->count() > 0)
<div class="card mt-2 mb-3">
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th colspan="4">Empresas</th>
            </tr>
            <tr>
                <th>Razão Social</th>
                <th>CNPJ</th>
                <th>Serviços</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($licitacao->empresas as $empresa)
            <tr>
                <td>{{ $empresa->razao_social }}</td>
                <td>{{ $empresa->cnpj }}</td>
                <td>
                    @if ($empresa->servicos->count() > 0)
                    <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#servicos-{{ $empresa->id }}" aria-expanded="false"
                        aria-controls="servicos-{{ $empresa->id }}">
                        <i class="fas fa-chevron-down"></i> Serviços
                    </button>
                    @else
                    <p> </p>
                    @endif
                </td>
                <td>
                    <a href="{{ route('empresas.show', $empresa) }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            <tr class="collapse" id="servicos-{{ $empresa->id }}">
                <td colspan="4">
                    <table class="table table-hover table-sm table-bordered mt-2">
                        <thead class="table-light">
                            <tr>
                                <th>Serviço</th>
                                <th>Unidade de Medida</th>
                                <th>Prazo (dias)</th>
                                <th>Valor Unitário (R$)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($empresa->servicos as $servico)
                            <tr>
                                <td>{{ $servico->descricao }}</td>
                                <td>{{ $servico->unidade_medida }}</td>
                                <td class="text-right">{{ $servico->prazo }}</td>
                                <td class="text-right"> {{ number_format($servico->valor_unitario, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif