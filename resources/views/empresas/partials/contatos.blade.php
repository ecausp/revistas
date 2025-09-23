@if ($empresa->contatos->count() > 0)
<div class="card mt-2 mb-3">
    <table class="table table-hover table-striped">
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
        @foreach ($contatos as $contato)
        <tbody>
            <td>{{ $contato->user->name }}</td>
            <td>{{ $contato->user->email }}</td>
            <td>{{ $contato->user->telefone }}</td>
            @endforeach
        </tbody>
    </table>
</div>

@endif