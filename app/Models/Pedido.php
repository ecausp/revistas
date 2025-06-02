<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
    'solicitante_id',
    'revista_id',
    'licitacao_id',
    'observacoes',
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
}