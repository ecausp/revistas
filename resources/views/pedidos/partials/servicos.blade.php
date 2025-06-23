@if ($pedido->servicos->count() > 0)
<div class="card mt-2 mb-3">
    <div class="card-header">
    <div class="d-flex align-items-center">
        <h5 class="mb-0 me-2 mr-5">Serviços</h5>
    </div>
</div>

    <table class="table datatable-simples dt-fixed-header dt-paging-50 responsive no-footer table-striped responsive mb-0">
        <thead>
            <tr>
                <th style="width: 11%;">Empresa</th>
                <th style="width: 20%;">Descrição</th>
                <th style="width: 11%;">Unidade de Medida</th>
                <th style="width: 11%;">Valor Unitário (R$)</th>
                <th style="width: 15%;">Quantidade</th>
                <th style="width: 11%;">Valor Total (R$)</th>
                <th style="width: 11%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
            $valorTotalPedido = 0;
            @endphp
            @foreach ($pedido->servicos as $servico)
            @php
            $valorTotalServico = $servico->pivot->quantidade * $servico->valor_unitario;
            $valorTotalPedido += $valorTotalServico;
            @endphp
            <tr id="servico-{{ $servico->id }}">
                <td>{{ $servico->empresas->first()->razao_social }}</td>
                <td>{{ $servico->descricao }}</td>
                <td>{{ $servico->unidade_medida }}</td>
                <td class="text-right">{{ number_format($servico->valor_unitario, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($servico->pivot->quantidade, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($valorTotalServico, 2, ',', '.') }}</td>
                <td>
                     <button type="button" class="btn btn-sm d-flex align-items-center gap-2 status-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#statusModal"
                            data-descricao="{{ $servico->descricao }}"
                            data-status="Em Andamento">
                            <i class="fas fa-plus text-warning border border-warning rounded-pill p-2 mr-2 " style="font-size: 1rem; width: 32px; height: 32px;"></i>
                            <span class="text-secondary" style="font-size: .9rem; font-style: italic;">Em Elaboração</span>
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"></td>
                <td class="text-right"><strong>Valor total do pedido: </strong></td>
                <td class="text-right"><strong>{{ number_format($valorTotalPedido, 2, ',', '.') }}</strong></td>
                <td>&nbsp;</td>   
            </tr>
        </tfoot>
    </table>
</div>
@endif


<!-- Modal de Status -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statusModalLabel">
                Status do Serviço: <span id="descricaoServicoModal" class="text-muted" style="font-style: italic;"></span>
        </h5>
      </div>
      <div class="modal-body">

        @include('pedidos.partials.status')
        @include('pedidos.partials.historico')
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

@section('javascripts_bottom')
<!-- Bootstrap JS (versão compatível com seu HTML) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    const statusButtons = document.querySelectorAll('.status-btn');
    const descricaoContainer = document.getElementById('descricaoServicoModal');

    statusButtons.forEach(button => {
      button.addEventListener('click', () => {
        const descricao = button.getAttribute('data-descricao');
        descricaoContainer.textContent = descricao;
      });
    });
  });

  /* 
  * Remover serviço 
  * Código retirado do pedidos/show.blade.php
  */
  $(document).on('click', '.remover-servico', function () {
      const button = $(this);
      const pedidoId = button.data('pedido-id');
      const servicoId = button.data('servico-id');

      if (!confirm('Tem certeza que deseja remover este serviço?')) return;

      $.ajax({
          url: `/pedidos/${pedidoId}/servicos/${servicoId}`,
          type: 'DELETE',
          data: {
              _token: '{{ csrf_token() }}'
          },
          success: function (resposta) {
              alert(resposta.mensagem);
              // Remove a linha do serviço
              $(`#servico-${servicoId}`).remove();
          },
          error: function () {
              alert('Erro ao remover o serviço. Tente novamente.');
          }
      });
  });  
</script>
@endsection
