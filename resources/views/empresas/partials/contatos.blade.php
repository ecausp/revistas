@if ($empresa->contatos->count() > 0)
<div class="card mt-2 mb-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th colspan="3">Contatos</th>
            </tr>
            <tr>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
            </tr>
        </thead>

        @foreach ($users as $user)
        <tbody>
            <td>{{ $user[0]->name }}</td>
            <td>{{ $user[0]->email }}</td>
            <td>{{ $user[0]->telefone }}</td>
            @endforeach
        </tbody>
    </table>
</div>

@endif