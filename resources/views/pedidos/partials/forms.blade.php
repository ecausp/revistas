<div class="md-6">

    <div class="mb-1">
        <label for="servico" class="form-label">Serviços</label>



        <select class="form-control select2" id="servico_id" name="servicos[]" multiple="multiple">
            @foreach ($servicos as $servico)
            @php
                if($servico->empresas->first() != null){
                    $dataEmpresa = $servico->empresas->first()->razao_social;
                }
                else
                {
                    $dataEmpresa = "Esse serviço não está associado a nenhuma empresa!";
                }

            @endphp

            <option value="{{ $servico->id }}" data-empresa="{{$dataEmpresa}}"
                data-descricao="{{ $servico->descricao }}" data-unidade="{{ $servico->unidade_medida }}"
                data-valor="{{ number_format($servico->valor_unitario, 2, ',', '.') }}">
                {{ $servico->descricao }} - {{ $servico->unidade_medida }}
            </option>

            @endforeach
        </select>

    </div>


    <div id="tabela-servicos-selecionados" class="mt-3"></div>

    <div class="mb-1">
        @if($servico->empresas->first() == null)
        <p style="color: red">Esse pedido se encontra irregular. <br>
        O Serviço solicitado não está associado a nenhuma empresa.
        Favor entrar em contato com o setor financeiro.</p>
        @else
        <label for="observacoes">Observações</label>
        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
        @endif
    </div>

    <div class="mb-1">
        <input type="hidden" class="form-control" name="revista_id" id="revista_id"
            value="{{ $solicitante->revista->id }}">
    </div>

    <div class="mb-1">
        <input type="hidden" class="form-control" name="solicitante_id" id="solicitante_id"
            value="{{ $solicitante->id }}">
    </div>

    <div class="mb-1">
        <input type="hidden" class="form-control" name="licitacao_id" id="licitacao_id" value="{{ $licitacao->id }}">
    </div>

    <div class="mt-5">
        @if($servico->empresas->first() == null)
        <button type="submit" class="btn btn-primary" disabled><i class="fas fa-save"></i> Salvar</button>
        @else
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        @endif
        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
            Voltar</a>

    </div>
</div>