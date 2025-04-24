<?php

namespace App\Models;

use App\Models\Licitacao;
use App\Models\Revista;
use App\Models\Servico;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Limite extends Model
{
    use HasFactory;
    // Nome da tabela
    protected $table = 'limites';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'licitacao_id',
        'revista_id',
        'servico_id',
        'quantidade',
        'valor_unitario',
    ];

    // Relação com Licitação
    public function licitacao()
    {
        return $this->belongsTo(Licitacao::class);
    }

    // Relação com Revista
    public function revista()
    {
        return $this->belongsTo(Revista::class);
    }

    // Relação com Serviço
    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }
}