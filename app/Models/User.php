<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use \Spatie\Permission\Traits\HasRoles;
    use \Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'codpes',
        'telefone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function criarUsuario($codpes, $nome, $email)
    {
        // busca pelo codpes pra ver se já existe
        $user = self::where('codpes', $codpes)->first();

        // caso não exista, cria um novo
        if (!$user) {
            $user = self::create([
                'name' => $nome,
                'email' => $email,
                'telefone' => 0,
                'codpes' => $codpes,
            ]);
        }

        return $user;
    }

    public function atribuirPermissaoRevista()
    {
        $this->givePermissionTo(
            Permission::where('guard_name', self::$hierarquiaNs)
            ->where('name', 'user')
            ->first()
        );

        $this->givePermissionTo(['revistas.revista']);
        $this->save();
    }
}