<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Empresa;
use App\Models\EmpresaLicitacaoServico;

class Licitacao extends Model
{
    use HasFactory;

    protected $table = 'licitacoes';

    protected $fillable = [
        'descricao',
        'ano',
        'editor',
        'valor_total',
    ];

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_licitacao')->withTimestamps();
    }
    
    // Serviços dessa licitação
    public function servicos()
    {
        return $this->hasMany(EmpresaLicitacaoServico::class);
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