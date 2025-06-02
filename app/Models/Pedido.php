<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    public function arquivos()
    {

        return $this->hasMany(Arquivo::class);

    }

    public function servicos()
    {
        return $this->hasMany(Servico::class);
    }

    public function revista()
    {
        return $this->belongsTo(Revista::class);
    }

    public function licitacao()
    {
        return $this->belongsTo(Licitacao::class);
    }

}