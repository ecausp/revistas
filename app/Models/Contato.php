<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Mail\ContatoCadastradoMail;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;

class Contato extends Model
{
    use HasFactory;


    protected $table = "contatos";
    protected $fillable = [
        "user_id", 
        "empresa_id",
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cria ou vincula um contato com um usuário e envia notificação por e-mail.
     */
    public static function criarComUsuario(array $dados): self
    {
        // Busca ou cria o usuário
        $user = User::firstOrCreate(
            ['email' => $dados['email']],
            [
                'name' => $dados['name'],
                'telefone' => $dados['telefone'],
                'codpes' => 0,
            ]
        );

        // Atualiza o telefone se já existia
        $user->update(['telefone' => $dados['telefone']]);

        // Garante as permissões corretas
        $user->givePermissionTo(
            Permission::where('guard_name', User::$hierarquiaNs)
                ->where('name', 'user')
                ->first()
        );

        $user->givePermissionTo(['revistas.empresa']);
        $user->save();

        // Cria ou recupera o contato vinculado
        $contato = self::firstOrCreate([
            'user_id' => $user->id,
            'empresa_id' => $dados['empresa_id'],
        ]);

        // Chama a função de notificação
        $contato->notificarContatoCadastrado();

        return $contato;
    }

    /**
     * Envia notificação de contato cadastrado.
     */
    public function notificarContatoCadastrado(): void
    {
        $empresa = Empresa::find($this->empresa_id);

        if ($empresa && $this->user) {
            Mail::to($this->user->email)->send(new ContatoCadastradoMail($empresa));
        }
    }
}