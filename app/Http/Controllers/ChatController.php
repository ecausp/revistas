<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

use App\Models\Pedido;

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

        if ($pedido->revista) {
            $usuarios = $usuarios->merge($pedido->revista->solicitantes);
        }

        if ($pedido->servico && $pedido->servico->empresa) {
            $usuarios = $usuarios->merge($pedido->servico->empresa->contatos);
        }

        return $usuarios->unique('id');
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
                Mail::to($user->email)->send(new ChatMail($pedido, $chat));
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'mensagem'  => 'required|string|max:2000',
        ]);

        $pedido = Pedido::with(['revista.solicitantes', 'servico.empresa.contatos'])->findOrFail($data['pedido_id']);
        $user = auth()->user();
        
        if ($pedido->status !== 'em_analise_empresa') {
            session()->flash('danger', 'Usuário não possui acesso a esse recurso. Favor contatar financeiro.');
            return redirect()->route('home');
        }
        
        else if (!$this->userCanAccessPedido($user, $pedido)) {
            session()->flash('danger', 'Usuário não possui acesso a esse recurso. Favor contatar financeiro.');
            return redirect()->route('home');
        }

        $data['user_id'] = $user->id;
        $chat = Chat::create($data);

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
