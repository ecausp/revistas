@if ($empresa->licitacoes->count() > 0)
<div class="card mt-2 mb-3">
    <x-table-simples>
        <thead>
            <tr>
                <th colspan="3">Licitações</th>
            </tr>
            <tr>
                <th>Descrição</th>
                <th>Ano</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($empresa->licitacoes as $licitacao)
            <tr>
                <td>{{ $licitacao->descricao }}</td>
                <td>{{ $licitacao->ano }}</td>
                <td><a href="{{ route('licitacoes.show', $licitacao) }}" class="btn btn-primary"><i
                            class="fas fa-eye"></i> </a></td>
            </tr>
            @endforeach
        </tbody>
    </x-table-simples>
</div>
@endif