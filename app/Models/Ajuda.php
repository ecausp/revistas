<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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

    protected function url(): Attribute
    {
        return Attribute::make(
            set: function (string $value) {
                // procura pelo padrão "v=CODIGO" das urls do youtube
                // se achar, pega o código, se não, mantém o original
                parse_str(parse_url($value, PHP_URL_QUERY), $params);
                return $params['v'] ?? $value;
            }
        );
    }
}
