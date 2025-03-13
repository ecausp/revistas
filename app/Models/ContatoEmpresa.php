<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContatoEmpresa extends Model
{
    use HasFactory;

    protected $table = "contato-empresa";
    protected $fillable = [
        'user_id',
        'empresa_id',
    ];
}