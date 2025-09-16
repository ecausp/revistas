<!-- resources/views/pedidos/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        <div class="d-flex align-items-center">
            Pedido {{ $pedido->id }}
            <i class="{{ $pedido->status->icon() }} ml-5 mr-2" style="{{ $pedido->status->icon_style() }}"></i>
            <span class="text-secondary" style="font-size: .9rem; font-style: italic;">{{ $pedido->status->label() }}</span>
        </div>
    </div>
    <div class="card-body">
        @include('pedidos.partials.status')
        @include('pedidos.partials.cabecalho')

        <p><strong>Observações:</strong> {{ $pedido->observacoes }}</p>
        <div id="tabela-servicos">
          @include('pedidos.partials.servicos')
        </div>
        
        @include('pedidos.partials.arquivos')

        @include('pedidos.partials.historico')
      </div>
</div>


<div class="mt-3">
    <small class="text-muted">Atualizado em {{ date_format($pedido->updated_at, 'd/m/Y H:i') }}
    <br />por {{ $pedido->solicitante->user->name }} - {{ $pedido->solicitante->user->email }}</small>
</div>

<div class="container mt-5 col">
    <!-- se o pedido ainda não foi enviado para a empresa, ele pode ser editado/deletado e arquivos podem ser adicionados; -->
    @if($pedido->status != "em_analise_empresa")
    <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-primary"><i class="fas fa-edit"></i>Editar</a>

    @if((auth()->user()->can('admin') || auth()->user()->can('acesso-revista')) && !auth()->user()->hasRole('financeiro'))
 
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adicionarArquivoModal">
            <i class="fas fa-paperclip"></i> Arquivo(s)
        </button>

    @endif

    @include('pedidos.partials.delete')
    @if((auth()->user()->can('admin') || auth()->user()->can('acesso-revista')) && !auth()->user()->hasRole('financeiro'))
    <form action="{{ route('pedidos.analise-empresa', $pedido) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-success" title="Enviar para análise da empresa.">
            <i class="fas fa-glasses"></i> Enviar
        </button>
    </form>
    @endif
    <!-- se o pedido já foi enviado para a empresa e o usuario que está visualizando o pedido é do financeiro ou tem acesso da empresa, pode devolver o pedido-->
    @elseif($pedido->status == "em_analise_empresa" && auth()->user()->hasRole('financeiro') || auth()->user()->can('acesso-empresa'))
        <!-- Botão que abre o modal para inserir o motivo da devolução-->
        <button type="button" class="btn btn-warning" title="Devolver para a revista" data-bs-toggle="modal" data-bs-target="#modalDevolver">
            <i class="fas fa-undo-alt"></i> Devolver
        </button>
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



<!-- Modal de motivo da devolução do pedido -->
<div class="modal fade" id="modalDevolver" tabindex="-1" aria-labelledby="modalDevolverLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="card mt-4">

                <div class="card-header d-flex align-items-center">
                    <span>Motivo da Devolução</span>
                </div>

                <div class="modal-body">
                    <form id="formDevolver" action="{{ route('pedidos.elaboracao', $pedido) }}" method="POST">
                    @csrf
                        <div class="mb-3">
                            <label for="observacao" class="form-label">Explique o motivo da devolução</label>
                            <textarea class="form-control" id="observacao" name="observacao" rows="4" required></textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning" title="Devolver para a revista" form="formDevolver">
                        <i class="fas fa-undo-alt"></i> Confirmar Devolução
                    </button>
                </div>
            </div>
        </div>
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
