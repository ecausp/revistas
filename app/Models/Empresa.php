<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Contato;
use App\Models\Licitacao;
use App\Models\EmpresaLicitacaoServico;

class Empresa extends Model
{
    use HasFactory;


    protected $table = 'empresas';
    protected $fillable = [
        'cnpj',
        'razao_social',
        'editor',
    ];

    public function licitacoes()
    {
        return $this->belongsToMany(Licitacao::class, 'empresa_licitacao');
    }
    public function servicos()
    {
        return $this->hasMany(EmpresaLicitacaoServico::class);
    }
    public function contatos()
    {

        return $this->hasMany(Contato::class);

    }

}