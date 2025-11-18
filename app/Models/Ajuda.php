<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ajuda extends Model
{
    protected $table = "ajuda";

    protected $fillable = [
        'categoria',
        'descricao',
        'url',
    ];
}
