<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Empresa extends Model
{
    use HasFactory;


    protected $table = 'empresas';
    protected $fillable = [
        'cnpj',
        'razao_social'
    ];

    public function licitacoes()
    {
        return $this->belongsToMany(Licitacao::class, 'empresa-licitacao');
    }

}