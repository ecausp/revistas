<?php

namespace App\Observers;

use App\Models\Pedido;
use App\Models\Saldo;
class PedidoObserver
{
    
    /**
     * o intuito aqui é usar o Observer para capturar as mudanças de estado do pedido
     * usamos isso para atualizar o valor_contratado do saldo da revista que fez o pedido
     */
    public function updated(Pedido $pedido): void
    {
        if($pedido->wasChanged('status')) {
            $revista = $pedido->revista;
            $licitacao = $pedido->licitacao;
            $saldo = Saldo::where('revista_id', $revista->id)
                ->where('licitacao_id', $licitacao->id)
                ->first();

            if($saldo) {
                $saldo->atualizaValorContratado();
            }
        }
    }
}
