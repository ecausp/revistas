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
            @foreach ($users as $user)
            <tr>
                <td>{{ $user[0]->name }}</td>
                <td>{{ $user[0]->email }}</td>
                <td>{{ $user[0]->telefone }}</td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endif