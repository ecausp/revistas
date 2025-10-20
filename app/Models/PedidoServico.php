<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Servico;
use \App\Models\Pedido;

class PedidoServico extends Model
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
}

