@props(['licitacao', 'empresas'])

<div class="modal fade" id="empresasModal{{ $licitacao->id }}" tabindex="-1" aria-labelledby="empresasModalLabel{{ $licitacao->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('licitacoes.addEmpresas', $licitacao) }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="empresasModalLabel{{ $licitacao->id }}">
                        Associar empresas à licitação
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Empresas vencedoras</label>

                        <select name="empresas[]" id="empresas{{ $licitacao->id }}" class="form-control select-empresas" multiple required>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}">
                                    {{ $empresa->razao_social }}
                                </option>
                            @endforeach
                        </select>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
