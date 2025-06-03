<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',       // ID do pedido relacionado
        'nome',    // Nome do arquivo salvo no storage
        'descricao',       // Descrição inserida pelo usuário
    ];
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}