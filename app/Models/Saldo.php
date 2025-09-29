<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    use HasFactory;

    protected $fillable = [
        'revista_id',
        'licitacao_id',
        'recurso_recebido',
        'recurso_estagio',
        'valor_contratado',
    ];

    public function revista()
    {
        return $this->belongsTo(Revista::class);
    }

    public function licitacao()
    {
        return $this->belongsTo(Licitacao::class);
    }
}
