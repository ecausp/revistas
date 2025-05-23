@if(!$revista->solicitantes->isEmpty())
<h5 class="mt-4">Solicitantes</h5>

<div class="card mt-2 mb-3">
    <table class="table table-striped">
        <thead colspan="3">
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Codpes</th>
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