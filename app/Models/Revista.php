<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
            'centro_despesa' => $request->centro_despesa,
            'responsavel_centro_despesa' => $responsavel,
            'email_revista' => $request->email_revista,
        ]);
    }

    public function atualizarRevista(Request $request, string $nomeResponsavel, string $codpes, string $email)
    {
        $responsavelAntigo = $this->responsavel_centro_despesa;

        // Atualiza dados principais
        $this->update([
            'editor' => $request->user()->id,
            'name' => $request->name,
            'centro_despesa' => $request->centro_despesa,
            'responsavel_centro_despesa' => $nomeResponsavel,
            'email_revista' => $request->email_revista,
        ]);

        // Se trocou responsável, faz os ajustes
        if ($responsavelAntigo !== $nomeResponsavel) {
            // Desvincula responsável antigo
            $this->removerResponsavelAntigo($responsavelAntigo);

            // Cria ou pega o novo responsável
            $novoUsuario = User::criarUsuario($codpes, $nomeResponsavel, $email);
            $novoUsuario->atribuirPermissaoRevista();

            // Vincula como solicitante
            Solicitante::criarSolicitante($novoUsuario, $this);
        }
    }

    protected function removerResponsavelAntigo($nomeAntigo)
    {
        $usuarioAntigo = User::where('name', $nomeAntigo)->first();

        if ($usuarioAntigo) {
            // remove vínculo de solicitante
            $this->solicitantes()->where('user_id', $usuarioAntigo->id)->delete();

            // remove permissão da revista
            $usuarioAntigo->revokePermissionTo('revistas.revista');
        }
    }


}