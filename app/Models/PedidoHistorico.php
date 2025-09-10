<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoHistorico extends Model
{
    use HasFactory;
    protected $table = 'pedido_historico';

    protected $fillable = [
        'id_pedido',
        'status_pedido',
        'id_pessoa',
        'observacao',
    ];


    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    public function pessoa()
    {
        return $this->belongsTo(User::class, 'id_pessoa');
    }
}
