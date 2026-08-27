<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Mail;
use App\Mail\ChatEnviadoMail;

class Chat extends Model
{
    use HasFactory;
    protected $fillable = [
        'pedido_id',
        'user_id',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

     /**
     * Cria a mensagem e dispara as notificações para os participantes.
     */
    public static function enviarMensagem(Pedido $pedido, User $autor, string $mensagem)
    {
        
        $chat = self::create([
            'pedido_id' => $pedido->id,
            'user_id'   => $autor->id,
            'message'   => $mensagem,
        ]);

        $chat->notificarParticipantes($pedido, $autor);

        return $chat;
    }

     /**
     * Retorna todos os usuários participantes do chat desse pedido.
     */
public static function participantesDoChat(Pedido $pedido)
{
    $usuarios = $pedido->revista->solicitantes->map(fn($s) => $s->user);

    $empresaIds = EmpresaLicitacaoServico::query()
        ->daEmpresaVigente()
        ->where('licitacao_id', $pedido->licitacao_id)
        ->whereIn('servico_id', $pedido->servicos->pluck('id'))
        ->pluck('empresa_id');

    $empresas = Empresa::whereIn('id', $empresaIds)->with('contatos.user')->get();
    foreach ($empresas as $empresa) {
        $usuarios = $usuarios->merge($empresa->contatos->map(fn($contato) => $contato->user));
    }

    return $usuarios->unique('id');
}

     /**
     * Envia e-mail para todos os participantes (exceto o autor).
     */
    public function notificarParticipantes(Pedido $pedido, User $autor)
    {
        foreach (self::participantesDoChat($pedido) as $user) {
            if ($user->id !== $autor->id) {
                Mail::to($user->email)->send(new ChatEnviadoMail($pedido, $this));
            }
        }
    }
}
