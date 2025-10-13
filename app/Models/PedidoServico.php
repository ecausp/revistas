<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];
}
