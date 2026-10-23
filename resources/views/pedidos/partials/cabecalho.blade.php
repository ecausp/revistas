@if(isset($pedido))
    <div class="row">
        <div class="col-md-6">
            <p><strong>Licitação:</strong> {{  $pedido->licitacao->descricao }} - {{ $pedido->licitacao->ano }} </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <p><strong>Revista:</strong> {{  $pedido->revista->name }} </p>
        </div>
        <div class="col-md-6">
            <p><strong>Solicitante:</strong> {{ $pedido->solicitante->user->name }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Editor(es):</strong>
                @foreach (explode(',', $pedido->revista->responsavel_centro_despesa) as $editorRevista)
                    {{ \Uspdev\Replicado\Pessoa::dump($editorRevista)['nompesttd'] }} @if (!$loop->last) , @endif
                @endforeach                
            </p>
        </div>
        
        @if($pedido->status == 'em_execucao')
            <div class="col-md-6">
                <p><strong>Número da Notificação de Fornecimento:</strong> {{ $pedido->num_notificacao_fornecimento }}</p>
            </div>
        @endif
    </div>

@else
    <div class="row">
        <div class="col-md-6">
            <p><strong>Licitação:</strong> {{  $licitacao->descricao }} - {{ $licitacao->ano }} </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <p>
                <strong>Revista:</strong> 
        @if(isset($revistasPermitidas) && $revistasPermitidas->isNotEmpty())
            <div class="w-100" style="max-width: 450px;"> 
                <form method="GET" action="{{ route('pedidos.create') }}" class="m-0 w-100">
                    <select name="revista_id" id="select-revista" class="form-control form-control-sm select2-revista">
                        @foreach($revistasPermitidas as $rev)
                            <option value="{{ $rev->id }}" {{ $solicitante->revista_id == $rev->id ? 'selected' : '' }}>
                                {{ $rev->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="d-none">Mudar</button>
                </form>
            </div>
        @else
            <input type="hidden" name="revista_id" value="{{ $solicitante->revista_id }}">
            {{ $solicitante->revista->name ?? 'Revista não encontrada' }}
        @endif
            </p>
        </div>
    </div>
@endif
