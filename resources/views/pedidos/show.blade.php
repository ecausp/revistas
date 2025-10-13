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

        @include('chat.view', ['pedido' => $pedido])

      </div>
</div>


<div class="mt-3">
    <small class="text-muted">Atualizado em {{ date_format($pedido->updated_at, 'd/m/Y H:i') }}
    <br />por {{ $pedido->solicitante->user->name }} - {{ $pedido->solicitante->user->email }}</small>
</div>

@include('pedidos.partials.botoes')


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

    $('#formDevolver').on('submit', function () {
      $('#formDevolver .milhares-mask').each(function () {
        let val = $(this).val().replace(/\./g, '');
        $(this).val(val);
      });
    });
  });
</script>
@endsection
