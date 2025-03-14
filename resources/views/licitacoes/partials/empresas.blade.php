@if ($licitacao->empresas->count() > 0)
    <div class="card mt-2 mb-3">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th colspan="3">Empresas</th>
                </tr>
                <tr>
                    <th>Razão Social</th>
                    <th>CNPJ</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($licitacao->empresas as $empresa)
                    <tr>
                        <td>{{ $empresa->razao_social }}</td>
                        <td>{{ $empresa->cnpj }}</td>
                        <td><a href="{{ route('empresas.show', $empresa) }}" class="btn btn-primary"><i class="fas fa-eye"></i> </a></td>
                    </tr>                     
                @endforeach                 
            </tbody>
        </table>   
    </div>
@endif 