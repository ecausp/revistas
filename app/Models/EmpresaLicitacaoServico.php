<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Empresa;
use App\Models\Licitacao;
use App\Models\Servico;

class EmpresaLicitacaoServico extends Model
{
    protected $table = 'empresa_licitacao_servico';
    protected $fillable = [
        'empresa_id',
        'licitacao_id',
        'servico_id',
        'valor_unitario',
        'prazo',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function licitacao()
    {
        return $this->belongsTo(Licitacao::class);
    }

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }
}
