{{-- Card de Trabalho Realizado --}}
@if(\App\Models\Contato::where('user_id', auth()->id())->exists() && $pedido->status instanceof \App\States\EmExecucao)
    <div class="card mt-4">
        <div class="card-header h5">
            Trabalho Realizado
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">
                Você pode anexar os arquivos do trabalho finalizado para que a revista verifique. Enquanto o pedido estiver nesta etapa, a revista também pode acompanhar o andamento e enviar mensagens pelo chat.
            </p>
            
            <p class="text-muted small mb-4">
                <i class="fas fa-info-circle"></i> <strong>Nota:</strong> A entrega por este meio não é obrigatória. Você pode combinar a entrega de outra forma com a revista.
            </p>

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
                    <button type="submit" class="btn btn-primary btn-enviar-trabalho" disabled>
                        <i class="fas fa-file-upload"></i> Enviar Trabalho
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif