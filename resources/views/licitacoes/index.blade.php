<!-- resources/views/licitacoes/index.blade.php -->
@extends('layouts.app')

@section('content')

@include('partials.messages')

<div class="card">
    <div class="card-header h5">
        Licitações
    </div>
    <div class="card-body">
        @if(auth()->user()->hasRole('financeiro'))
            <a href="{{ route('licitacoes.create') }}" class="btn btn-primary mb-3"><i class="fas fa-plus"></i>
            Licitação</a>
        @endif

        <x-table>
            <thead>
                <tr>
                    <th>Descrição</th>
                    <th>Ano</th>
                    <th>Valor Total</th>
                    <th>Empresas Associadas</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($licitacoes as $licitacao)
                <tr>
                    <td>{{ $licitacao->descricao }} - {{ $licitacao->ano }}</td>
                    <td>{{ $licitacao->ano }}</td>
                    <td class="text-right">{{ number_format($licitacao->valor_total, 2, ',', '.') }}</td>

                    <td>
                        @if ($licitacao->empresas->isNotEmpty())
                            @foreach ($licitacao->empresas as $empresa)
                                <span>{{ $empresa->razao_social }} ; </span>
                            @endforeach
                        @else
                            <span>Nenhuma empresa associada!</span>
                        @endif
                    </td>


                    <td>
                        <a href="{{ route('licitacoes.show', $licitacao) }}" class="btn btn-primary"><i
                                class="fas fa-eye"></i> </a>
                        @if(auth()->user()->hasRole('financeiro'))
                        <a href="{{ route('licitacoes.edit', $licitacao) }}" class="btn btn-primary"><i
                                class="fas fa-edit"></i></a>

                        <button type="button" class="btn btn-primary" title="Associar Empresas à Licitação" data-bs-toggle="modal" data-bs-target="#empresasModal{{ $licitacao->id }}">
                            <i class="fas fa-building"></i>
                        </button>
                    
                        <a href="{{ route('saldos.create', ['licitacao' => $licitacao->id]) }}" class="btn btn-primary">
                            <i class="fas fa-money-bill"></i></a>

                        <!-- Inclui o modal partial -->
                        @include('licitacoes.partials.modal-empresas', ['licitacao' => $licitacao, 'empresas' => $empresas])
                        @endif        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </x-table>
    </div>
    @endsection
    
@section('javascripts_bottom')
    @parent
    <script>
    $(document).ready(function() {
        $('.select-empresas').select2({
            width: '100%',
            placeholder: 'Selecione as empresas'
        });
    });
</script>
@endsection


@section('javascripts_bottom')
@parent
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
