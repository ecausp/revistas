<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Solicitante;
use App\Models\Contato;
use App\Models\Empresa;
use App\Models\Pedido;
class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $solicitante = (($user) ? Solicitante::where('user_id', $user->id)->first() : null);
        // se o usuário que está logando é solicitante de uma revista, então redireciona ele pra visualizar a revista dele;
        if ($solicitante) {
            return redirect()->route('revistas.show', $solicitante->revista_id);
        }
        
        // se o usuário que está logando é contato de uma empresa, então redireciona ele para visualizar a lista de pedidos;
        $contato = (($user) ? Contato::where('user_id', $user->id)->first() : null);
        if ($contato)
        {
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
            $servicos = $empresa->servicos->pluck('id');
            $pedidos = Pedido::whereHas('servicos', function ($query) use ($servicos) {
                $query->whereIn('servicos.id', $servicos);})->get();
            return view('pedidos.index', compact('pedidos'));
        }
        return view('welcome');
    }
}
