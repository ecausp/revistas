@if ($empresa->contatos->count() > 0)
<div class="card mt-2 mb-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th colspan="3">Contatos</th>
            </tr>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($empresa->contatos as $contato)
            <tr>
                <td>{{ $contato->name }}</td>
                <td>{{ $contato->email }}</td>
                <td>{{ $contato->telefone }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif