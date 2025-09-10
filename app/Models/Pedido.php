<?php

namespace App\Models;

use App\States\StatusPedido;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

class Pedido extends Model
{
    use HasFactory;
    use HasStates;

    protected $fillable = [
    'solicitante_id',
    'revista_id',
    'licitacao_id',
    'observacoes',
    'status',
];
    public function arquivos()
    {

        return $this->hasMany(Arquivo::class);

    }

    public function revista()
    {
        return $this->belongsTo(Revista::class);
    }

    public function solicitante()
    {
        return $this->belongsTo(Solicitante::class);
    }

    public function licitacao()
    {
        return $this->belongsTo(Licitacao::class);
    }

        public function servicos()
    {
        return $this->belongsToMany(Servico::class, 'pedido_servico')->withPivot('quantidade')->withTimestamps();
    }

    public function historicos()
    {
        return $this->hasMany(PedidoHistorico::class, 'id_pedido');
    }

    protected function casts(): array
    {
        return [
            'status' => StatusPedido::class,
        ];
    }

}