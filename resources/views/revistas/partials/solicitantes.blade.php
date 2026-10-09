@if(!$revista->solicitantes->isEmpty())

<div class="card mt-2 mb-3">
    <div class="card-header font-weight-bold">Solicitantes</div>
    <div class="card-body p-0">
        <x-table-simples>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Nº USP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revista->solicitantes as $solicitante)
                    @if (!in_array(intval($solicitante->user->codpes), explode(',', $revista->responsavel_centro_despesa)))
                        <tr>
                            <td>{{ $solicitante->user->name ?? 'N/A' }}</td>
                            <td>{{ $solicitante->user->email ?? 'N/A' }}</td>
                            <td>{{ $solicitante->user->codpes ?? 'N/A' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </x-table-simples>
    </div>
</div>
@endif
