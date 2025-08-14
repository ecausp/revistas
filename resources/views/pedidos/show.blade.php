<!-- resources/views/pedidos/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        <div class="d-flex align-items-center">
            Pedido {{ $pedido->id }}
            <i class="{{ $pedido->status->icon() }}" style="{{ $pedido->status->icon_style() }}"></i>
            <span class="text-secondary" style="font-size: .9rem; font-style: italic;">{{ $pedido->status->label() }}</span>
        </div>
    </div>
    <div class="card-body">
        @include('pedidos.partials.cabecalho')

        <p><strong>Observações:</strong> {{ $pedido->observacoes }}</p>
        <div id="tabela-servicos">
          @include('pedidos.partials.servicos')
        </div>
        
        @include('pedidos.partials.arquivos')
    
      </div>
</div>

<div class="container mt-5 col">
    @if($pedido->status != "em_analise_empresa")
    <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
        Editar</a>

    @php
        $user = auth()->user();
    @endphp

        @if(($user->can('admin') || $user->can('acesso-revista')) && !$user->hasRole('financeiro'))
 
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adicionarArquivoModal">
        <i class="fas fa-paperclip"></i> Arquivo(s)
    </button>

        @endif

    @include('pedidos.partials.delete')

    <form action="{{ route('pedidos.analise-empresa', $pedido) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success" title="Enviar para análise da empresa.">
            <i class="fas fa-glasses"></i> Enviar
        </button>
    </form>

    @else
    <form action="{{ route('pedidos.elaboracao', $pedido) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-warning" title="Retornar para elaboracao.">
            <i class="fas fa-chevron-left"></i> Devolver
        </button>
    </form>
    @endif
    <a href="{{ route('pedidos.index') }}" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
        Voltar</a>

</div>

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

@endsection

@section('javascripts_bottom')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Tradução para português do Brasil -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
  $(document).ready(function () {
    $('.milhares-mask').mask('#.##0', { reverse: true });

    $('form').on('submit', function () {
      $('.milhares-mask').each(function () {
        let val = $(this).val().replace(/\./g, '');
        $(this).val(val);
      });
    });
  });
</script>
@endsection
