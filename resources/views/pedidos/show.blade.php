@extends('layouts.app')
@section('content')

<div class="card">
    <div class="card-header h5">
        <div class="d-flex align-items-center">
            Pedido {{ $pedido->id }}
            <i class="{{ $pedido->status->icon() }} ml-4 mr-2" style="{{ $pedido->status->icon_style() }}"></i>
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
        @include('pedidos.partials.trabalho_realizado')
        @include('pedidos.partials.historico')
        @include('pedidos.partials.modal_empenho')
        
         
         <div id="chatContainer" class="no-print">
            @include('chat.view', ['pedido' => $pedido])
        </div>
        
    </div>
</div> 

<div class="mt-3 pl-3">
    <small class="text-muted">Atualizado em {{ date_format($pedido->updated_at, 'd/m/Y H:i') }}
    <br />por {{ $pedido->solicitante->user->name }} - {{ $pedido->solicitante->user->email }}</small>
</div>

<div class="row">
    @include('pedidos.partials.botoes')
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
                    @php
                    $devolucao = "";
                    
                    if ($pedido->status == "em_analise_empresa" || $pedido->status == "em_analise_financeiro") {
                        $devolucao = 'pedidos.elaboracao';
                    }
                    else if ($pedido->status = "em_conferencia") {
                        $devolucao = 'pedidos.devolver-execucao';
                    }


                    @endphp
                    <form id="formDevolver" action="{{ route($devolucao, $pedido) }}" method="POST">
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
    @parent

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

    <script>
    document.getElementById('btnPdf')?.addEventListener('click', function () {
        // Esconde o chat para impressão
        document.querySelector('#chatContainer')?.classList.add('no-print');

        window.print();

        // Volta a exibir o chat depois da impressão
        document.querySelector('#chatContainer')?.classList.remove('no-print');
    });
    </script>

    <style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
    </style>
    <script>
    $(document).ready(function() {
        // Lógica para exibir nomes de múltiplos arquivos selecionados no card de trabalho realizado
        $('#arquivos').on('change', function() {
            const files = this.files;
            const $display = $('#file-names-display');
            $display.empty();

            if (files.length > 0) {
                let fileListHtml = `<p><strong>${files.length} arquivo(s) selecionado(s):</strong></p>
                    <table class="table table-sm table-striped small mb-0">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Arquivo</th>
                                <th style="width: 15%;">Tamanho</th>
                                <th style="width: 45%;">Descrição</th> 
                            </tr>
                        </thead>
                        <tbody>`;

                for (let i = 0; i < files.length; i++) {
                    const fileSizeMB = (files[i].size / 1024 / 1024).toFixed(2);
                    
                    fileListHtml += `<tr>
                        <td><i class="fas fa-file-alt mr-1"></i> ${files[i].name}</td>
                        <td>${fileSizeMB} MB</td>
                        <td><input type="text" name="descricoes[]" class="form-control form-control-sm" placeholder="Obrigatório"></td>
                    </tr>`;
                }

                fileListHtml += '</tbody></table>';
                $display.html(fileListHtml);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Função que verifica o botão
        function verificarBotao() {
            let todasPreenchidas = true;
            let temArquivos = false;

            // Verifica todos os inputs de descrição gerados dinamicamente
            $('input[name="descricoes[]"]').each(function() {
                temArquivos = true;
                if ($(this).val().trim() === '') {
                    todasPreenchidas = false;
                    return false; // Sai do loop
                }
            });

            // Se não houver arquivos selecionados ou alguma descrição vazia, desabilita
            if (!temArquivos || !todasPreenchidas) {
                $('.btn-enviar-trabalho').prop('disabled', true);
            } else {
                $('.btn-enviar-trabalho').prop('disabled', false);
            }
        }

        // Quando o usuário digita na descrição, verifica
        $(document).on('input', 'input[name="descricoes[]"]', function() {
            verificarBotao();
        });

        // Quando o usuário seleciona o arquivo, espera um pouquinho e verifica
        $('#arquivos').on('change', function() {
            setTimeout(verificarBotao, 200); // 200ms para garantir que a tabela foi gerada
        });
        
        // Verifica ao carregar a página
        setTimeout(verificarBotao, 200);
    });
</script>
@endsection