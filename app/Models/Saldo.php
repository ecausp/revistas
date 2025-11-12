<?php

namespace App\Models;

use App\States\Concluido;
use App\States\EmConferencia;
use App\States\EmExecucao;
use App\States\EmPagamento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    use HasFactory;

    protected $fillable = [
        'revista_id',
        'licitacao_id',
        'recurso_recebido',
        'recurso_estagio',
        'valor_contratado',
    ];

    public function revista()
    {
        return $this->belongsTo(Revista::class);
    }

    public function licitacao()
    {
        return $this->belongsTo(Licitacao::class);
    }

    public function atualizaValorContratado()
    {
        $pedidos = $this->revista->pedidos ?? collect();

        $estadosValidos = [
            EmExecucao::class,
            EmConferencia::class,
            EmPagamento::class,
            Concluido::class,
        ];

        $total = 0;

        foreach($pedidos as $pedido)
        {
            if(in_array(get_class($pedido->status), $estadosValidos))
            {
                foreach($pedido->servicos as $servico)
                {
                    $total += $servico->pivot->quantidade * $servico->pivot->valor_unitario;
                }
            }
        }
        
        $this->valor_contratado = $total;
        $this->save();

        \Log::info("Saldo #{$this->id} atualizado: valor_contratado = {$total}");

    }
}
