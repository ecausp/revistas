<!-- resources/views/pedidos/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        Pedido {{ $pedido->id }}
    </div>
    @can("acesso-revista")
    <div class="card-body">
        @include('pedidos.partials.cabecalho')

        <p><strong>Observações:</strong> {{ $pedido->observacoes }}</p>

        @include('pedidos.partials.servicos')
    </div>




@if ($pedido->arquivos->count() > 0)
<div class="card mt-4">
<div class="card-header">
    <div class="d-flex align-items-center">
        <h5 class="mb-0 me-2 mr-5">Arquivos Anexados</h5>
        @include('pedidos.partials.botao')
    </div>
</div>
    <table class="table table-bordered mb-0">
        <thead>
            <tr>
                <th style="width: 33%;">Arquivo</th>
                <th style="width: 33%;">Descrição</th>
                <th style="width: 33%;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pedido->arquivos as $arquivo)
            <tr>
                <td>{{ $arquivo->nome }}</td>
                <td>{{ $arquivo->descricao }}</td>
                <td class="d-flex gap-2">
                    <a href="{{ asset('storage/' . $arquivo->path) }}" target="_blank"
                        class="btn btn-primary d-flex ml-2 mr-2 justify-content-center">
                        <i class="fas fa-eye"></i>
                    </a>
                    <form action="{{ route('arquivos.destroy', $arquivo) }}" method="POST"
                        onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger d-flex justify-content-center">
                            <i class="fa fa-times"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif



</div>

<div class="container mt-5 col">
    <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
        Editar</a>

    @include('pedidos.partials.delete')

    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>

</div>
@endcan
<!-- Modal -->
<div class="modal fade" id="adicionarArquivoModal" tabindex="-1" aria-labelledby="adicionarArquivoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('arquivos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adicionarArquivoModalLabel">Adicionar Arquivo</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="arquivo" class="form-label">Arquivo</label>
                        <input class="form-control" type="file" name="arquivo" accept=".pdf,.jpg,.jpeg,.png,.svg,.tiff"
                            required>
                        <small class="form-text text-muted">Máx: 3MB</small>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <input class="form-control" type="text" name="descricao" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascripts_bottom')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection