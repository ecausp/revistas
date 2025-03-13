<!-- resources/views/licitacoes/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        {{ $licitacao->descricao }}
    </div>
    @can('admin')
    <div class="card-body">
        <p><strong>Ano:</strong> {{ $licitacao->ano }}</p>
        <p><strong>Valor Total:</strong> {{ number_format($licitacao->valor_total, 2, ',', '.') }}</p>

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

        <small class="text-muted">Atualizado em {{ date_format($licitacao->updated_at, 'd/m/Y H:i') }}
            <br />por {{ $licitacao->editor }}</small>

        <div class="container mt-5 col">
            <a href="{{ route('licitacoes.edit', $licitacao) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                Editar</a>

            @include('licitacoes.partials.delete')

            <a href="{{ route('licitacoes.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                Voltar</a>

        </div>   

    </div>
    @endcan
</div>
@endsection