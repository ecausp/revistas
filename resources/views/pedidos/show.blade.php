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

        @include('pedidos.partials.arquivos')
    </div>







</div>

<div class="container mt-5 col">
    <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
        Editar</a>


    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#adicionarArquivoModal">
        <i class="fas fa-paperclip"></i> Arquivo(s)
    </button>

    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#adicionarServicoModal">
        <i class="fas fa-wrench"></i> Servicos(s)
    </button>


    @include('pedidos.partials.delete')

    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>

</div>
@endcan
<!-- Modal de Arquivos -->
<div class="modal fade" id="adicionarArquivoModal" tabindex="-1" aria-labelledby="adicionarArquivoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('arquivos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adicionarArquivoModalLabel">Adicionar Arquivo</h5>
                </div>
                <div class="modal-body">
                    <!-- Formulário de upload -->
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Serviços -->
<div class="modal fade" id="adicionarServicoModal" tabindex="-1" aria-labelledby="adicionarServicoModal" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('home', $pedido) }}">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="adicionarServicoModal">Editar Serviços do Pedido</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="servicos" class="form-label">Serviços</label>
            <select id="servicos" name="servicos[]" class="form-select select2" multiple required>
              @foreach($servicos as $servico)
                <option value="{{ $servico->id }}"
                    {{ $pedido->servicos->contains($servico->id) ? 'selected' : '' }}>
                  {{ $servico->descricao }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Salvar</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </form>
  </div>
</div>


@endsection

@section('javascripts_bottom')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Selecione os serviços",
            width: '100%'
        });
    });
</script>
@endsection