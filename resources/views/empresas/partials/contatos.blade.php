@if ($empresa->contatos->count() > 0)
<div class="card mt-2 mb-3">
    <x-table-simples>
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
        <tbody>
            @foreach ($contatos as $contato)
            <tr>
                <td>{{ $contato->user->name }}</td>
                <td>{{ $contato->user->email }}</td>
                <td>{{ $contato->user->telefone }}</td>
            </tr>
            @endforeach
        </tbody>
    </x-table-simples>
</div>

@endif
