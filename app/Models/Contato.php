<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contato extends Model
{
    use HasFactory;


    protected $table = 'contatos';
    protected $fillable = [
        'contato_id', 
        "empresa_id",
    ];

    public function post()
    {

        return $this->belongsTo(Empresa::class);

    }
}