<div class="row">
    <div class="col-md-6">

        <div class="mb-1">
            <label for="cnpj" class="form-label">CNPJ</label>
            <input type="text" class="form-control cnpj" name="cnpj" id="cnpj"
                value="{{ old('cnpj', isset($empresa) ? $empresa->cnpj : '') }}" placeholder="00.000.000/0000-00"
                required>
        </div>

        <div class="mb-1">
            <label for="razao_social" class="form-label">Razão Social</label>
            <input type="text" class="form-control" name="razao_social" id="razao_social"
                value="{{ old('razao_social', isset($empresa) ? $empresa->razao_social : '') }}" required>
        </div>

        <div class="mb-1">
            <label for="licitacao_id" class="form-label">Licitação</label>

            <!-- se houver uma empresa associada, significa que é edição; então bloqueia a parte de licitacao; -->
            @if (isset($empresa))
            @php
            $licitacao_id = \App\Models\EmpresaLicitacao::all()->where('empresa_id', '=',
            $empresa->id)->first()->licitacao_id;
            $licitacao = \App\Models\Licitacao::find($licitacao_id);
            @endphp
            <select class="form-control select2" name="licitacao_id" id="licitacao_id">
                <option value="{{ $licitacao->id }}" selected>{{ $licitacao->descricao }} - {{ $licitacao->ano }}
                </option>
                @else
                <select class="form-control select2" name="licitacao_id" id="licitacao_id">
                    @endif
                    @foreach ($licitacoes as $licitacao)
                    <option value="{{ $licitacao->id }}">{{ $licitacao->descricao }} - {{ $licitacao->ano }}</option>
                    @endforeach
                </select>
        </div>
    </div>

    <div class="col-md-6">

        <label for="servico" class="form-label">Serviço</label>
        <div id="servicos-container">
            <div class="mb-3 d-flex align-items-center">
                <div class="w-75 mr-3">
                    <select class="form-control select2" name="servico_id[]" id="servico_id" multiple="multiple">
                        @foreach ($servicos as $servico)
                        <option value="{{ $servico->id }}" @if (isset($empresa))@if(in_array($servico->id,
                            $servicos_associados)) selected
                            @endif @endif>
                            {{ $servico->descricao }} - {{ $servico->unidade_medida }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-success" id="servico-modal" title="Criar novo serviço">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5 col">
    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>

    @if (isset($empresa))
    <a href="{{ route('contatos.create',  $empresa)}}" class="btn btn-success"><i class="fas fa-user"></i>
        Contato(s)</a>
    @endif

    <a href="{{ route('empresas.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i> Voltar</a>

</div>