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
            <p><strong>Centro de Despesa:</strong> {{ $pedido->revista->centro_despesa }} </p>
        </div>
        <div class="col-md-6">
            <p><strong>Editor(es):</strong>
                @foreach (explode(',', $pedido->revista->responsavel_centro_despesa) as $editorRevista)
                    {{ \Uspdev\Replicado\Pessoa::dump($editorRevista)['nompesttd'] }} @if (!$loop->last) , @endif
                @endforeach                
            </p>
        </div>
    </div>

@else
    <div class="row">
        <div class="col-md-6">
            <p><strong>Licitação:</strong> {{  $licitacao->descricao }} - {{ $licitacao->ano }} </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <p><strong>Revista:</strong> {{  $solicitante->revista->name }} </p>
        </div>
        <div class="col-md-6">
            <p><strong>Solicitante:</strong> {{ $solicitante->user->name }}</p>
        </div>
        <div class="col-md-6">
            <p><strong>Centro de Despesa:</strong> {{ $solicitante->revista->centro_despesa }} </p>
        </div>
        <div class="col-md-6">
            <p><strong>Editor:</strong>
                {{ $solicitante->revista->responsavel_centro_despesa }} </p>
        </div>
    </div>

@endif