<div class="modal fade" id="observacaoSaldoModal{{ $saldo->id }}" tabindex="-1" role="dialog"
    aria-labelledby="observacaoSaldoModalLabel{{ $saldo->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="observacaoSaldoModalLabel{{ $saldo->id }}">
                    Observação do recurso
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $saldo->observacao }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
