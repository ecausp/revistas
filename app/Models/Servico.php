<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\EmpresaLicitacaoServico;

class Servico extends Model
{
    use HasFactory;

    protected $table = 'servicos';
    protected $fillable = [
        "descricao",
        "unidade_medida",
        "editor"
    ];

    public function empresasLicitacoes()
    {
        return $this->hasMany(EmpresaLicitacaoServico::class);
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