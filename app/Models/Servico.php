<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    protected $table = 'servicos';
    protected $fillable = [
        "descricao",
        "quantidade",
        "unidade_medida",
        "valor_unitario",
        "editor",
        "prazo",
    ];

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_servico');
    }

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_servico')->withPivot('quantidade')->withTimestamps();
    }
    public function limites()
    {
        return $this->hasMany(Limite::class);
    }

}