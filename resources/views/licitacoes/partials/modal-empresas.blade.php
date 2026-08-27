@props(['licitacao', 'empresas'])

<div class="modal fade" id="empresasModal{{ $licitacao->id }}" tabindex="-1" role="dialog"
    aria-labelledby="empresasModalLabel{{ $licitacao->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <form method="POST" action="{{ route('licitacoes.addEmpresas', $licitacao) }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="empresasModalLabel{{ $licitacao->id }}">
                        Classificar empresas da licitação
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center">Participante</th>
                                    <th>Empresa</th>
                                    <th class="text-center">Classificação</th>
                                    <th class="text-center">Vigente</th>
                                    <th>Motivo da substituição</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($empresas as $empresa)
                                    @php
                                        $empresaAssociada = $licitacao->empresas->firstWhere('id', $empresa->id);
                                        $associada = $empresaAssociada !== null;
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            @if ($associada)
                                                <input type="checkbox" checked disabled>
                                                <input type="hidden" name="empresas[{{ $empresa->id }}][selecionada]" value="1">
                                            @else
                                                <input type="checkbox" class="empresa-selecao"
                                                    name="empresas[{{ $empresa->id }}][selecionada]" value="1">
                                            @endif
                                        </td>
                                        <td>{{ $empresa->razao_social }}</td>
                                        <td>
                                            <input type="number" class="form-control empresa-dado"
                                                name="empresas[{{ $empresa->id }}][classificacao]" min="1"
                                                value="{{ $empresaAssociada?->pivot->classificacao }}"
                                                @unless($associada) disabled @endunless required>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" class="empresa-dado"
                                                name="empresa_vigente" value="{{ $empresa->id }}"
                                                @checked($empresaAssociada?->pivot->vigente)
                                                @unless($associada) disabled @endunless required>
                                        </td>
                                        <td>
                                            <textarea class="form-control empresa-dado" rows="1"
                                                name="empresas[{{ $empresa->id }}][motivo_substituicao]"
                                                placeholder="Obrigatório quando a vigente não for a 1ª colocada"
                                                @unless($associada) disabled @endunless>{{ $empresaAssociada?->pivot->motivo_substituicao }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <p class="mb-2">
                        Se a empresa não foi encontrada, utilize o botão abaixo para cadastrá-la:
                    </p>

                    <a href="{{ route('empresas.create') }}" class="btn btn-outline-primary" target="_blank">
                        <i class="fas fa-plus"></i> Cadastrar nova empresa
                    </a>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
