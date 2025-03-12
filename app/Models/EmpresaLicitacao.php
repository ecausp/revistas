<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaLicitacao extends Model
{
    use HasFactory;
    protected $table = 'empresa-licitacao';
    protected $fillable = [
        'empresa_id',
        'licitacao_id'
        ];
}