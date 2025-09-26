<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revista extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "centro_despesa",
        "editor",
        "responsavel_centro_despesa",
    ];

    public function solicitantes()
    {

        return $this->hasMany(Solicitante::class);
    }
    
    public function pedidos()
    {

        return $this->hasMany(Pedido::class);

    }

    public function saldos()
    {
        return $this->hasMany(Saldo::class);
    }
    
}