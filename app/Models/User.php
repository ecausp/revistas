<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Uspdev\Replicado\Pessoa;

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

    public static function criarUsuario($codpes)
    {
        // busca pelo codpes pra ver se já existe
        $user = self::where('codpes', $codpes)->first();
        
        // caso não exista, busca dados e cria
        if (!$user) {
            $dados = Pessoa::dump($codpes);

            $user = self::create([
                'name' => $dados['nompesttd'],
                'email' => Pessoa::email($codpes),
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

    public function revistasVisiveis()
    {
        // se o usuário é admin ou usuario, pode ver todas as revistas
        if ($this->hasAnyRole(['admin', 'financeiro'])) {
            return Revista::all();
        }
        //se o usuário é solicitante de uma revista, só pode ver a revista que ele faz parte
        if (Solicitante::where('user_id', $this->id)->exists()) {
            $revistaId = Solicitante::where('user_id', $this->id)->first()->revista_id;
            return Revista::where('id', $revistaId)->get();
        }
        // se não se encaixa em nenhum criterio, não pode visualizar nenhuma revista
        return collect();
    }
}
