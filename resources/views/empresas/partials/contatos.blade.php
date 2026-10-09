@if ($empresa->contatos->count() > 0)
<div class="card mt-2 mb-3">
    <div class="card-header font-weight-bold">Contatos</div>
    <div class="card-body p-0">
        <x-table-simples>
            <thead>
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
</div>

@endif
