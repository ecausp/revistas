<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\States\StatusPedido;
use Spatie\ModelStates\HasStates;


class PedidoHistorico extends Model
{
    use HasFactory;
    use HasStates;
    protected $casts = [
        'status_pedido' => StatusPedido::class,
    ];
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