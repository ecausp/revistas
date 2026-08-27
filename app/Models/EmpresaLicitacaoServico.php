<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use App\Models\Empresa;
use App\Models\Licitacao;
use App\Models\Servico;

class EmpresaLicitacaoServico extends Model
{
    use HasFactory;
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

    public function scopeDaEmpresaVigente(Builder $query): Builder
    {
        return self::aplicarFiltroDaEmpresaVigente($query);
    }

    public static function aplicarFiltroDaEmpresaVigente($query)
    {
        return $query->where(function ($query) {
            $query->whereExists(function ($subquery) {
                $subquery->selectRaw('1')
                    ->from('empresa_licitacao')
                    ->whereColumn(
                        'empresa_licitacao.empresa_id',
                        'empresa_licitacao_servico.empresa_id'
                    )
                    ->whereColumn(
                        'empresa_licitacao.licitacao_id',
                        'empresa_licitacao_servico.licitacao_id'
                    )
                    ->where('empresa_licitacao.vigente', true);
            })->orWhereNotExists(function ($subquery) {
                $subquery->selectRaw('1')
                    ->from('empresa_licitacao')
                    ->whereColumn(
                        'empresa_licitacao.licitacao_id',
                        'empresa_licitacao_servico.licitacao_id'
                    );
            });
        });
    }
}
