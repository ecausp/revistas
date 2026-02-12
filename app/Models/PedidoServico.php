<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use \App\Models\Servico;
use \App\Models\Pedido;

class PedidoServico extends Pivot
{
    use HasFactory;
    protected $table = 'pedido_servico';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pedido_id',
        'servico_id',
        'quantidade',
        'valor_unitario',
        'num_empenho',
        'data_vencimento'
    ];

        public function servico()
    {
        return $this->belongsTo(Servico::class, 'servico_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function diasRestantesBadge()
    {
        $hoje = \Carbon\Carbon::today();
        $vencimento = \Carbon\Carbon::parse($this->data_vencimento)->startOfDay();
        $diasRestantes = $hoje->diffInDays($vencimento, false);
        //dd($hoje, $vencimento, $diasRestantes);
        if ($diasRestantes <= 0) {
            $class = 'bg-danger text-white ml-1';
            $text = '0';
        } elseif ($diasRestantes <= 7) {
            $class = 'bg-warning text-dark ml-1';
            $text = "$diasRestantes";
        } else {
            $class = 'bg-info text-white ml-1';
            $text = "$diasRestantes";
        }

        return "<span class=\"badge $class\">$text</span>";
    }
}

