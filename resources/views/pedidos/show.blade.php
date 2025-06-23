<!-- resources/views/pedidos/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header h5">
        <div class="d-flex align-items-center">
            Pedido {{ $pedido->id }}
            <i class="fas fa-plus text-warning border border-warning rounded-pill p-2 mr-2 ml-5" style="font-size: 1rem; width: 32px; height: 32px;"></i>
            <span class="text-secondary" style="font-size: .9rem; font-style: italic;">Em Andamento</span>
        </div>
    </div>
    @can("acesso-revista")
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
    <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
        Editar</a>
    @php
        $user = auth()->user();
    @endphp

    @if(($user->can('admin') || $user->can('acesso-revista')) && !$user->hasRole('financeiro'))
    
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adicionarServicoModal">
        <i class="fas fa-wrench"></i> Serviço(s)
    </button>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adicionarArquivoModal">
        <i class="fas fa-paperclip"></i> Arquivo(s)
    </button>

    @endif

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
    <form id="form-servicos" method="POST" action="{{ route('pedidos.updateServicos', $pedido) }}">
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
           <!-- Aqui vai aparecer a tabela com as quantidades -->
  <div id="tabela-quantidades" class="mt-4">
    <!-- JS preenche isso -->
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

<script>
let quantidadesIniciais = {!! json_encode(
    $pedido->servicos->mapWithKeys(function ($servico) {
        return [$servico->id => $servico->pivot->quantidade];
    })
) !!};

$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Selecione os serviços",
        width: '100%',
        language: "pt-BR"
    });

    $('#form-servicos').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr('action');
        const data = form.serialize();

        $.ajax({
            url: url,
            type: 'PUT',
            data: data,
            success: function(response) {
                $('#tabela-servicos').html(response.html);

                // Atualiza quantidades iniciais com o que vem do backend (se quiser)
                quantidadesIniciais = response.quantidades;

                // Fecha o modal
                const modalEl = document.getElementById('adicionarServicoModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            },
            error: function() {
                alert('Erro ao atualizar os serviços. Verifique os dados e tente novamente.');
            }
        });
    });

    $('#adicionarServicoModal').on('shown.bs.modal', function () {
        atualizarTabelaQuantidades();
    });
});

function atualizarTabelaQuantidades() {
    const selecionados = $('#servicos').select2('data');
    let html = '';

    if (selecionados.length > 0) {
        html += '<table class="table table-bordered">';
        html += '<thead><tr><th>Serviço</th><th>Quantidade</th></tr></thead><tbody>';

        selecionados.forEach(item => {
            const valor = quantidadesIniciais[item.id] ?? 1;

            html += `
                <tr>
                    <td>${item.text}</td>
                    <td>
                        <input style="text-align: right;" type="text" class="milhares-mask" name="quantidades[${item.id}]" class="form-control" value="${valor}" required>
                    </td>
                </tr>
            `;
        });

        html += '</tbody></table>';
    }

    $('#tabela-quantidades').html(html);
    
    $('.milhares-mask').mask('#.##0', {
        reverse: true
    });    
}

</script>
@endsection
