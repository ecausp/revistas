@if(!$revista->solicitantes->isEmpty())

<div class="card mt-2 mb-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th colspan="3">Solicitantes</th>
            </tr>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>CODPES</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revista->solicitantes as $solicitante)
            <tr>
                <td>{{ $solicitante->user->name ?? 'N/A' }}</td>
                <td>{{ $solicitante->user->email ?? 'N/A' }}</td>
                <td>{{ $solicitante->user->codpes ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif