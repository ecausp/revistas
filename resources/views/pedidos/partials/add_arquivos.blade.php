@php
    // Verifica se a variável $pedido existe e tem ID, indicando que estamos na Edição.
    $isEdit = isset($pedido) && $pedido->id;
    
    // Define a rota de ação para o formulário de upload (apenas na Edição)
    $uploadRoute = $isEdit ? route('pedidos.store-arquivo', $pedido->id) : null;
@endphp

{{-- ABRE O FORMULÁRIO SEPARADO APENAS NA EDIÇÃO, MIRANDO A ROTA DE UPLOAD --}}
@if($isEdit)
<form action="{{ $uploadRoute }}" method="POST" enctype="multipart/form-data" id="form-arquivos">
    @csrf
@endif

{{-- ADICIONANDO MARGEM: Ajustando classes para restaurar o visual --}}
<div class="card mt-4 ml-2"> 
    <div class="card-header h6">
        Anexar Arquivos (Opcional)
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label for="arquivos" class="form-label">Arquivos (Máx: 3MB por arquivo)</label>
           <input class="form-control" type="file" name="arquivos[]" id="arquivos" multiple
                accept=".pdf,.jpg,.jpeg,.png,.svg,.tiff,.doc,.docx,.xls,.xlsx">
            <small class="form-text text-muted">Selecione um ou mais arquivos. Tipos permitidos: PDF, Imagem (.jpg, .jpeg, .png, .svg, .tiff), Word (.doc, .docx) e Excel (.xls, .xlsx).</small>
            <div id="file-names-display" class="mt-2 small text-secondary"></div>
        </div>
        
        {{-- BOTÃO DE ANEXAR - AJUSTADO PARA COR PRIMARY E ALINHAMENTO ESQUERDO --}}
        @if($isEdit)
        <div class="mt-4"> {{-- Removido justify-content-end --}}
            <button type="submit" id="btn-anexar-arquivos" class="btn btn-primary" disabled>
                <i class="fas fa-file-upload"></i> Anexar Arquivos
            </button>
        </div>
        @endif
    </div>
</div>

{{-- FECHA O FORMULÁRIO APENAS SE ELE FOI ABERTO --}}
@if($isEdit)
</form>
@endif

 {{-- Card de Trabalho Realizado --}}
    @if(\App\Models\Contato::where('user_id', auth()->id())->exists() && $pedido->status instanceof \App\States\EmExecucao)
        <div class="mt-4">
            <p class="text-muted mb-3">
                Você pode anexar os arquivos do trabalho finalizado para que a revista verifique. Enquanto o pedido estiver nesta etapa, a revista também pode acompanhar o andamento e enviar mensagens pelo chat.
            </p>
                
            <p class="text-muted small mb-4">
                <i class="fas fa-info-circle"></i> <strong>Nota:</strong> A entrega por este meio não é obrigatória. Você pode combinar a entrega de outra forma com a revista.
            </p>

            <div class="card mt-4 ml-2"> 
                <div class="card-header h6">
                    Anexar Trabalho Realizado
                </div>
                <div class="card-body">
                    <form action="{{ route('pedidos.store-arquivo', $pedido->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="origem" value="empresa">
                            
                        <div class="mb-3">
                            <label for="arquivos" class="form-label">Arquivos (Máx: 3MB por arquivo)</label>
                                <input class="form-control" type="file" name="arquivos[]" id="arquivos" multiple
                                    accept=".pdf,.jpg,.jpeg,.png,.svg,.tiff,.doc,.docx,.xls,.xlsx">
                                <small class="form-text text-muted">Selecione um ou mais arquivos. Tipos permitidos: PDF, Imagem (.jpg, .jpeg, .png, .svg, .tiff), Word (.doc, .docx) e Excel (.xls, .xlsx).</small>
                                <div id="file-names-display" class="mt-2 small text-secondary"></div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-upload"></i> Enviar Arquivo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    @endif