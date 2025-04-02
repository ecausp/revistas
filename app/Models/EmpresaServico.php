<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaServico extends Model
{
    use HasFactory;
    protected $table = 'empresa_servico';
    protected $fillable = [
        'empresa_id',
        'servico_id'
        ];
}