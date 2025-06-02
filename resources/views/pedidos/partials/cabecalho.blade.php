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
                <p><strong>Responsável pelo Centro de Despesa:</strong>
                    {{ $solicitante->revista->responsavel_centro_despesa }} </p>
            </div>
        </div>