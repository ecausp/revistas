@if ($empresa->licitacoes->count() > 0)
<div class="card mt-2 mb-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th colspan="3">Serviços</th>
            </tr>
            <tr>
                <th>Descrição</th>
                <th>Valor Unitário</th>
                <th>Unidade de Medida</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($empresa->servicos as $servico)
            <tr>
                <td>{{ $servico->descricao }}</td>
                <td>{{ $servico->valor_unitario }}</td>
                <td>{{ $servico->unidade_medida }}</td>
                <td><a href="{{ route('servicos.show', $servico) }}" class="btn btn-primary"><i class="fas fa-eye"></i>
                    </a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif