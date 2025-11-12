<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Pedido;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    // garante que os métodos só podem ser acessados por um usuário logado;
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Pedido $pedido)
    {
        $data = $request->validate([
            'message'  => 'required|string|max:2000',
        ]);

        $user = auth()->user();

        if ($pedido->status->label() == "Em Elaboração") {
            session()->flash('danger', 'O chat está indisponível para o pedido no status de elaboração');
            return redirect()->route('home');
        }
        
        $this->authorize('view', $pedido);

        Chat::enviarMensagem($pedido, $user, $data['message']);

        return redirect()->back()->with('success', 'Mensagem enviada!');
    }

    public function show($pedidoId)
    {
        $pedido = Pedido::with([
            'revista.solicitantes',
            'servico.empresa.contatos',
            'chats.user'
        ])->findOrFail($pedidoId);

        $this->authorize('view', $pedido);

        return view('chat.show', compact('pedido'));
    }
}
