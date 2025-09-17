<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

use App\Models\Pedido;

use App\Mail\ChatEnviadoMail;
use Illuminate\Support\Facades\Mail;
class ChatController extends Controller
{
    // garante que os métodos só podem ser acessados por um usuário logado;
    public function __construct()
    {
        $this->middleware('auth');
    }

    // lista todos os usuários que podem participar do chat;
    private function participantesDoChat($pedido)
    {
        $usuarios = collect();

        $usuarios = $pedido->revista->solicitantes->map(fn($s) => $s->user);

        foreach ($pedido->servicos as $servico) {
            foreach ($servico->empresas as $empresa) {
                $usuarios = $usuarios->merge(
                    $empresa->contatos->map(fn($c) => $c->user)
                );
            }
        }
        return $usuarios;
    }


    // verifica se o usuario logado pode acessar o chat
    private function podeAcessarChat($user, $pedido): bool
    {
        return $this->participantesDoChat($pedido)->contains('id', $user->id);
    }

    // envia e-mail para os participantes que há uma nova mensagem no chat.
    private function notificarParticipantes($chat, $pedido, $autor)
    {
        foreach ($this->participantesDoChat($pedido) as $user) {
            if ($user->id !== $autor->id) {
                Mail::to($user->email)->send(new ChatEnviadoMail($pedido, $chat));
            }
        }
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'message'  => 'required|string|max:2000',
        ]);

        $user = auth()->user();
        if ($pedido->status->label() != "Em Análise (Empresa)") {
            session()->flash('danger', 'Status errado');
            return redirect()->route('home');
        }
        
        else if (!$this->podeAcessarChat($user, $pedido)) {
            session()->flash('danger', 'Usuário não possui acesso a esse recurso. Favor contatar financeiro.');
            return redirect()->route('home');
        }

        $data['user_id'] = $user->id;
            
        $chat = Chat::create([
                'pedido_id' => $pedido->id,
                'user_id'  => $user->id,
                'message'  => $data['message'],
    ]);

        $this->notificarParticipantes($chat, $pedido, $user);

        return redirect()->back()->with('success', 'Mensagem enviada!');
    }

    public function show($pedidoId)
    {
        $pedido = Pedido::with([
            'revista.solicitantes',
            'servico.empresa.contatos',
            'chats.user'
        ])->findOrFail($pedidoId);

        $user = auth()->user();

        if (!$this->podeAcessarChat($user, $pedido)) {
            session()->flash('danger', 'Usuário não possui acesso a esse recurso. Favor contatar financeiro.');
            return redirect()->route('home');
        }

        return view('chat.show', compact('pedido'));
    }


    public function edit(Chat $chat)
    {
        //
    }

    public function update(Request $request, Chat $chat)
    {
        //
    }


    public function destroy(Chat $chat)
    {
        //
    }
}
