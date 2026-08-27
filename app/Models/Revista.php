<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Uspdev\Replicado\Pessoa;
class Revista extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "centro_despesa",
        "editor",
        "responsavel_centro_despesa",
        "email_revista",
    ];

    protected $hidden = [
        'centro_despesa',
    ];

    public function solicitantes()
    {

        return $this->hasMany(Solicitante::class);
    }
    
    public function pedidos()
    {

        return $this->hasMany(Pedido::class);

    }

    public function saldos()
    {
        return $this->hasMany(Saldo::class);
    }
    
    public static function criarRevista(Request $request, string $responsavel)
    {
        return self::create([
            'editor' => $request->user()->id,
            'name' => $request->name,
            'responsavel_centro_despesa' => $responsavel,
            'email_revista' => $request->email_revista,
        ]);
    }

    public function atualizarRevista(Request $request, string $responsaveisString)
    {
        // transforma string em array
        $novos = explode(',', $responsaveisString);
        $antigos = explode(',', $this->responsavel_centro_despesa);

        // atualiza dados principais da revista
        $this->update([
            'editor' => $request->user()->id,
            'name' => $request->name,
            'responsavel_centro_despesa' => $responsaveisString,
            'email_revista' => $request->email_revista,
        ]);

        // Remove responsáveis que saíram
        $remover = array_diff($antigos, $novos);
        foreach ($remover as $codpes) {
            $usuario = User::where('codpes', $codpes)->first();
            if ($usuario) {
                // remove vínculo de solicitante
                $this->solicitantes()->where('user_id', $usuario->id)->delete();
                // remove permissão da revista
                $usuario->revokePermissionTo('revistas.revista');
            }
        }

        // Adiciona novos responsáveis
        $adicionar = array_diff($novos, $antigos);
        foreach ($adicionar as $codpes) {
            $usuario = User::criarUsuario($codpes);
            $usuario->atribuirPermissaoRevista();
            Solicitante::criarSolicitante($usuario, $this);
        }
    }


    public static function conveterResponsaveis(array $responsaveis)
    {
        // o select2 retorna uma array com o codpes de todos os editores,
        // precisamos então converter para string para salvar no banco de dados.
        return(implode(',', $responsaveis));
    }

}
