<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licitacao extends Model
{
    use HasFactory;

    protected $table = 'licitacoes';

    protected $fillable = [
        'descricao',
        'ano',
        'valor_total',
        'editor',
    ];

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa-licitacao');
    }

    public function pedido()
    {
        return $this->hasOne(Pedido::class);
    }

    public function saldos()
    {
        return $this->hasMany(Saldo::class);
    }
}