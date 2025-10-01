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

    public function revista()
    {
        return $this->belongsTo(Revista::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public static function criarSolicitante(User $user, Revista $revista)
    {
        return self::create([
            'user_id' => $user->id,
            'revista_id' => $revista->id,
        ]);
    }
    

}