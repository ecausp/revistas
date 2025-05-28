        <div class="row">
            <div class="col-md-6 mb-3">
                <p><strong>Revista:</strong> $revista->name </p>
            </div>
            <div class="col-md-6 mb-3">
                <p><strong>Solicitante:</strong> {{ auth()->user()->name }}</p>
            </div>
            <div class="col-md-6 mb-3">
                <p><strong>Centro de Despesa:</strong> $revista->centro_despesa </p>
            </div>
            <div class="col-md-6 mb-3">
                <p><strong>Responsável pelo Centro de Despesa:</strong> $revista->responsavel_centro_despesa </p>
            </div>
        </div>