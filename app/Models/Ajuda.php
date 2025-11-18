<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\CategoriaAjuda;

class Ajuda extends Model
{
    protected $table = "ajuda";

    protected $fillable = [
        'categoria',
        'descricao',
        'url',
    ];

    protected function casts(): array
    {
        return [
            'categoria' => CategoriaAjuda::class,
        ];
    }
}
