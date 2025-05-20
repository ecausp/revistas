<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitante extends Model
{
    use HasFactory;


    protected $table = 'solicitantes';
    protected $fillable = [
        'user_id', 
        "revista_id",
    ];

    public function post()
    {

        return $this->belongsTo(Revista::class);

    }
}